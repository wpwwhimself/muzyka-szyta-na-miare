-- users
SELECT
    k.id "id",
    k.hasło "password",
    null "remember_token",
    fq.date "created_at",
    fq.date "updated_at"
FROM p_klienci k
LEFT JOIN (
    select q.klient_id, min(q.data_1) "date"
    from p_questy q
    group by q.klient_id
    ) fq ON fq.klient_id = k.id
