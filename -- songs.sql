-- songs
SELECT
    p.tytuł as "title",
    COALESCE(p.album, '-do uzupełnienia-') as "artist",
    null as "cover_artist",
    '-do uzupełnienia-' as "link",
    1 as "quest_type_id",
    '-do uzupełnienia-' as "genre",
    '-do uzupełnienia-' as "price_code",
    q.zyczenia as 'notes'
FROM p_questy q
LEFT JOIN p_projekty p ON p.id = q.id