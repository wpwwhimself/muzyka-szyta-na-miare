library(stringr); library(lubridate); library(tidyverse)
library(dotenv); library(RMySQL); library(rjson)

as.pln <- function(x){
    x %>%
    as.numeric() %>%
    format(nsmall = 2, big.mark = " ", decimal.mark = ",") %>%
    paste("zł")
}
percentage <- function(number, total){
    paste(
        number,
        " <small>(",
        round(number / total * 100),
        "%)</small>",
        sep = ""
    )
}

#### połączenie ####
dotenv::load_dot_env(".env")
conn <- dbConnect(
    MySQL(),
    user = Sys.getenv("STATS_DB_USERNAME"),
    password = Sys.getenv("STATS_DB_PASSWORD"),
    dbname = Sys.getenv("STATS_DB_DATABASE"),
    host = Sys.getenv("STATS_DB_HOST")
)

#### zaktualizuj dane ####
tables <- c(
    "clients", "genres", "prices", "quest_types",
    "quests", "requests", "settings", "song_work_times",
    "songs", "status_changes", "statuses"
)
for (i in seq_along(tables)) {
  assign(
    tables[i],
    tbl(conn, tables[i]) %>%
      collect() %>%
      mutate(across(matches(c("date")), as_date)) %>%
      mutate(across(matches(c("created_at", "updated_at")), as_datetime)) %>%
      mutate(across(matches(c("time_spent")), hms))
  )
}

#### pre calc ####

summary_elapsed <- interval(as.Date("2020-01-01"), as.Date(today())) %>% as.period()
recent_income <- status_changes %>%
    filter(
        date >= today() - years(1) &
        new_status_id == 32
    ) %>%
    mutate(comment = as.numeric(comment)) %>%
    group_by(month = floor_date(date, "month")) %>%
    summarise(
        sum = sum(comment),
        mean = mean(comment)
    ) %>%
    mutate(month = paste(
        month(month),
        substr(year(month), 3, 4),
        sep = "-"
    ))

#### actual list ####

#TODO###############
# * ile razy wpadały poprawki do zlecenia - średnio i max w tym miesiącu
# * średni czas z podziałem na gatunki z porównaniem sekund do zeszłego miesiąca
# * na ile dni przed deadlinem oddaję pierwszą wersję
# * jak często przekraczam termin klienta (zaakceptowany vs data hard deadline)
#TODO###############

list(
    today = today() %>% as.character(),
    summary = list(
        general = list(
            "biznes kręci się od" = paste(
                year(summary_elapsed), "y ",
                month(summary_elapsed), "m ",
                day(summary_elapsed), "d",
                sep = ""
            ),
            "skończone questy" = quests %>%
                filter(status_id == 19) %>%
                nrow(),
            "poznani klienci" = clients %>% nrow(),
            "zarobki w sumie" = status_changes %>%
                filter(new_status_id == 32) %>%
                mutate(comment = as.numeric(comment)) %>%
                summarise(sum = sum(comment)) %>%
                as.pln()
        ),
        quest_types = list(
            split = quests %>%
                mutate(song_type = substr(song_id, 1, 1)) %>%
                left_join(quest_types, c("song_type" = "code")) %>%
                count(type, sort = TRUE) %>%
                pull(name = type),
            total = quests %>% nrow()
        ),
        quest_pricings = list(
            split = prices %>%
                pull(indicator, indicator) %>%
                map( #zebranie utworów posiadających dany indicator
                    ~ songs %>%
                    filter(str_detect(price_code, .x)) %>%
                    nrow()
                ) %>%
                unlist() %>%
                mutate(prices, songs_count = .) %>%
                arrange(-songs_count) %>%
                head() %>%
                pull(songs_count, service),
            total = songs %>%
                filter(str_detect(price_code, "^\\d*\\.\\d*$", negate = TRUE)) %>%
                nrow()
        )
    ),
    recent = list(
        quests = list(
            main = c(
                "nowe" = quests %>% filter(created_at >= today() - months(1)) %>% nrow(),
                "ukończone" = status_changes %>% filter(new_status_id == 19, date >= today() - months(1)) %>% nrow(),
                "debiutanckie" = clients %>% filter(created_at >= today() - months(1)) %>% nrow()
            ),
            compared_to = c(
                "nowe" = quests %>% filter(created_at >= today() - months(2) & created_at < today() - months(1)) %>% nrow(),
                "ukończone" = status_changes %>% filter(new_status_id == 19, date >= today() - months(2) & date < today() - months(1)) %>% nrow(),
                "debiutanckie" = clients %>% filter(created_at >= today() - months(2) & created_at < today() - months(1)) %>% nrow()
            )
        )
    ),
    clients = list(
        summary = list(
            split = list(
                "zaufani" = clients %>% filter(trust == 1) %>% nrow(),
                "krętacze" = clients %>% filter(trust == -1) %>% nrow(),
                "patroni" = clients %>% filter(helped_showcasing == 2) %>% nrow(),
                "bez zleceń" = clients %>%
                    anti_join(quests %>% filter(status_id == 19), c("id" = "client_id")) %>%
                    nrow(),
                "kobiety" = clients %>%
                    tidyr::separate(client_name, c("first_name", "last_name"), " ") %>%
                    mutate(gender = if_else(str_sub(first_name, -1) == "a", "f", "m") %>% as.factor()) %>%
                    count(gender) %>%
                    filter(gender == "f") %>%
                    pull(n)
            ),
            total = clients %>% nrow()
        ),
        exp = list(
            split = clients %>%
                left_join(quests, c("id" = "client_id"), keep = TRUE) %>%
                filter(status_id == 19) %>%
                count(client_id) %>%
                mutate(exp = if_else(
                        n >= 10, 1, if_else(
                        n >= 4, 2, if_else(
                        n >= 2, 3, if_else(
                        n >= 1, 4, 5
                        )))
                    ) %>%
                    factor(1:5, c("weterani (10+)", "biegli (4-9)", "zainteresowani (2-3)", "nowicjusze (1)", "debiutanci (0)"))
                ) %>%
                count(exp) %>%
                pull(n, exp),
            total = clients %>% nrow()
        ),
        new = clients %>%
            filter(created_at >= today() - years(1)) %>%
            count(month = floor_date(created_at, "month")) %>%
            mutate(month = paste(
                month(month),
                substr(year(month), 3, 4),
                sep = "-"
            )) %>%
            pull(n, month)
    ),
    income = list(
        total = recent_income %>% pull(sum, month),
        prop = recent_income %>% pull(mean, month) %>% round(2)
    ),
    songs = list(
        time_summary = list(
            "średnio na całość" = song_work_times %>%
                group_by(song_id) %>%
                mutate(time_spent = time_spent %>% period_to_seconds()) %>%
                summarise(time_spent = sum(time_spent)) %>%
                summarise(mean = mean(time_spent)) %>%
                seconds_to_period() %>%
                round() %>%
                as.character(),
            "średnio elementów" = song_work_times %>%
                count(song_id) %>%
                filter(n > 1) %>%
                summarise(mean = mean(n)) %>%
                as.numeric() %>%
                round(2)
        ),
        time_genres = song_work_times %>%
            left_join(songs, c("song_id" = "id")) %>%
            group_by(song_id, genre_id) %>%
            mutate(time_spent = time_spent %>% period_to_seconds()) %>%
            summarise(time_spent = sum(time_spent)) %>%
            group_by(genre_id) %>%
            summarise(mean = mean(time_spent) %>% round() %>% seconds_to_period()) %>%
            inner_join(genres, c("genre_id" = "id")) %>%
            arrange(mean) %>%
            mutate(mean = as.character(mean)) %>%
            pull(mean, name)
    )
) %>%
    toJSON(indent = "1") %>%
    write("storage/app/stats.json")

#### plotting (not used) ####

song_work_times %>%
    left_join(songs, c("song_id" = "id")) %>%
    group_by(song_id, genre_id) %>%
    mutate(time_spent = time_spent %>% period_to_seconds()) %>%
    summarise(
        time_spent = sum(time_spent) / 60,
        parts = n()
    ) %>%
    inner_join(genres, c("genre_id" = "id")) %>%
    ggplot(aes(reorder(name, time_spent, FUN = median), time_spent, group = genre_id)) +
    geom_boxplot()
