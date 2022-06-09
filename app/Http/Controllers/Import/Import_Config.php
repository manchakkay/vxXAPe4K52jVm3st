<?php

namespace App\Http\Controllers\Import;

// Конфигурация импорта
class Import_Config
{
    /**
     * С каких доменов пропускаются ссылки на файлы
     * Полезно использовать во избежания импорта "пикселей" систем аналитики
     */
    const restricted_hosts = [
        "rs.mail.ru",
        "facebook.com",
        "mc.yandex.ru",
    ];

    const mysql_queries = [
        // post_id {integer}[1...]
        "get_thumbnail" =>
        "
            SELECT DISTINCT
                p.ID AS pid, a.ID AS aid, p.guid AS pslug
            FROM
                wp_posts AS p
            JOIN
                wp_posts AS a
            ON
                p.ID = a.post_parent
            JOIN
                wp_postmeta AS m
            ON
                a.ID = m.meta_value
            WHERE
                (p.post_type = 'news') AND
                (p.post_status != 'private') AND
                (p.post_status != 'trash') AND
                (a.post_type = 'attachment') AND
                (m.meta_key = '_thumbnail_id') AND
                (p.ID = '%d')
            ;
        ",
        // post_type {string}[news, page], post_id {integer}[1...]
        "get_all_files" =>
        "
            SELECT DISTINCT
                p.id,
                a.id AS aid,
                a.guid,
                a.post_mime_type
            FROM
                wp_posts AS p
            JOIN
                wp_posts AS a
            ON
                p.ID = a.post_parent
            WHERE
                (p.post_type = '%s') AND
                (p.post_status != 'private') AND
                (p.post_status != 'trash') AND
                (a.post_type = 'attachment') AND
                (p.ID = '%d')
            ;
        ",
        // no inputs
        "get_all_news" => "
            SELECT
                *
            FROM
                wp_posts
            WHERE
                (post_type = 'news') AND
                (post_status = 'publish')
            ORDER BY
                id ASC
            ;
        ",
        // no inputs
        "get_all_pages" => "
            SELECT
                *
            FROM
                wp_posts
            WHERE
                (post_type = 'page') AND
                (post_status = 'publish')
            ORDER BY
                id ASC
            ;
        ",
        // news_id {int}
        "get_one_news" => "
            SELECT
                *
            FROM
                wp_posts
            WHERE
                (post_type = 'news') AND
                (post_status = 'publish') AND
                (ID = %d)
            ORDER BY
                id ASC
            ;
        ",
        // news_id {int}
        "get_one_page" => "
            SELECT
                *
            FROM
                wp_posts
            WHERE
                (post_type = 'page') AND
                (post_status = 'publish') AND
                (ID = %d)
            ORDER BY
                id ASC
            ;
        ",
        // file_url {string}
        "get_file_by_url" => "
            SELECT id,
                MATCH (files.source_url) AGAINST (
                    '%s WITH QUERY EXPANSION'
                ) as coeff,
                ABS(
                    LENGTH(files.source_url) - LENGTH(
                        '%s'
                    )
                ) as length
            FROM
                files
            WHERE
                INSTR(
                    LOWER(
                        '%s'
                    ),
                    REVERSE(
                        SUBSTRING(
                            REVERSE(LOWER(files.source_url)),
                            LOCATE(
                                '.',
                                REVERSE(LOWER(files.source_url))
                            ) + 1,
                            CHAR_LENGTH(LOWER(files.source_url))
                        )
                    )
                )
                ORDER BY
                    coeff DESC,
                    length ASC;
        ",
    ];
}
