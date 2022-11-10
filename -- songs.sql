-- songs
SELECT
    null as "id",
    COALESCE(p.tytuł, CONCAT('-do uzupełnienia-', q.id)) as "title",
    COALESCE(p.album, CONCAT('-do uzupełnienia-', q.id)) as "artist",
    null as "cover_artist",
    null as "link",
    CONCAT('-do uzupełnienia-', q.id) as "genre",
    -- CONCAT('-do uzupełnienia-', q.cena) as "price_code",
    q.zyczenia as 'notes'
FROM p_questy q
LEFT JOIN p_projekty p ON p.id = q.id