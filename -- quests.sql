-- quests
SELECT
    q.id as "id",
    1 as "quest_type_id",
    1 as "song_id",
    q.klient_id as "client_id",
    (CASE
        WHEN p.status = 0 THEN 19
        WHEN p.status = 1 THEN CASE
            WHEN q.data_3 is not null THEN 16
            WHEN q.data_2 is not null THEN 15
            ELSE 12
            END
        WHEN p.status = 2 THEN 18
        ELSE 11
    END) as "status_id",
    q.cena as "price_code_override",
    q.cena as "price",
    (CASE
        WHEN q.data_5 is not null THEN 1
        ELSE 0
    END) as "paid",
    q.deadline as "deadline",
    null as "hard_deadline",
    q.data_1 as "created_at",
    COALESCE(GREATEST(q.data_4, q.data_5), q.data_2, q.data_1) as "updated_at"
FROM p_questy q
LEFT JOIN p_projekty p ON p.id = q.id