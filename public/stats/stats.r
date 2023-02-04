library(stringr); library(lubridate); library(tidyverse)
library(dotenv); library(RMySQL); library(rjson)

as.pln <- function(x){
    x %>%
    as.numeric() %>%
    format(nsmall = 2, big.mark = " ", decimal.mark = ",") %>%
    paste("zł")
}

#### połączenie ####
dotenv::load_dot_env("../../.env")
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
      mutate(across(matches(c("created_at", "updated_at", "date"))))
  )
}

#### pre calc ####

summary_elapsed <- interval(as.Date("2020-01-01"), as.Date(today())) %>% as.period()
summary_elapsed <- paste(year(summary_elapsed), "lat,", month(summary_elapsed), "mies. i", day(summary_elapsed), "dni")
quest_count <- quests %>% filter(status_id == 19) %>% count() %>% as.numeric()
gains_total <- status_changes %>%
    filter(new_status_id == 32) %>%
    mutate(comment = as.numeric(comment)) %>%
    summarise(sum = sum(comment)) %>%
    as.pln()
summary <- list(elapsed = summary_elapsed, quest_count = quest_count, gains_total = gains_total)

recent <- c(
    new = quests %>% filter(created_at >= today() - months(1)) %>% count() %>% as.numeric(),
    done = status_changes %>% filter(new_status_id == 19, date >= today() - months(1)) %>% count() %>% as.numeric(),
    fresh = clients %>% filter(created_at >= today() - months(1)) %>% count() %>% as.numeric()
)

clients_exp <- quests %>%
  filter(status_id == 19) %>%
  left_join(clients, c(client_id = "id")) %>%
  group_by(client_name) %>%
  count() %>%
  arrange(-n)

clients_gender <- clients %>%
  select(client_name) %>%
  tidyr::separate(client_name, c("first_name", "last_name"), " ") %>%
  mutate(gender = if_else(str_sub(first_name, -1) == "a", "f", "m") %>% as.factor()) %>%
  group_by(gender) %>%
  count() %>%
  pull(n, name = "gender")

list(
    today = today() %>% as.character(),
    summary = summary,
    recent = recent,
    clients_gender = clients_gender
) %>%
    toJSON(indent = "1") %>%
    write("public/stats/stats.json")
