-- clients
SELECT
    k.id "id",
    (CASE
        WHEN k.nazwisko REGEXP '/' THEN SUBSTRING(k.nazwisko, 1, POSITION('/' IN k.nazwisko)-1)
        ELSE k.nazwisko
    END) "client_name",
    (CASE
    	WHEN k.kontakt REGEXP '@' THEN k.kontakt
     	ELSE null
    END) "email",
    k.tel "phone",
    null "other_medium",
    (CASE k.contactpreference
    	WHEN 'w' THEN 'whatsapp'
     	WHEN 'f' THEN 'facebook'
     	WHEN 's' THEN 'sprzedajemy'
     	WHEN 't' THEN 'telefon'
     	ELSE 'email'
    END) "contact_preference",
    -k.kio "trust",
    k.budget "budget",
    k.default_wishes "default_wishes",
    null "special_prices",
    fq.date "created_at",
    fq.date "updated_at"
FROM p_klienci k
LEFT JOIN (
    select q.klient_id, min(q.data_1) "date"
    from p_questy q
    group by q.klient_id
    ) fq ON fq.klient_id = k.id
WHERE k.id != 1
