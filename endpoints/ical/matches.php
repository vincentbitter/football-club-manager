<?php

if (! defined('ABSPATH')) exit;

function fcmanager_register_matches_ical_endpoint()
{
    add_rewrite_tag('%team_slug%', '([^&]+)');
    add_rewrite_tag('%fcm_ics%', '([0-1])');

    add_rewrite_rule(
        '^teams/([^/]+)/matches\.ics$',
        'index.php?team_slug=$matches[1]&fcm_ics=1',
        'top'
    );
}

add_action('template_redirect', function () {
    if (get_query_var('fcm_ics') !== '1') {
        return;
    }

    $team_slug = get_query_var('team_slug');

    $team = get_page_by_path($team_slug, OBJECT, 'fcmanager_team');
    if (!$team) {
        status_header(404);
        exit;
    }

    $matches = fcmanager_get_upcoming_matches($team->ID, 100);

    $ics = fcmanager_build_ics($team, $matches);

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename="' .  esc_attr(sanitize_title($team->post_title)) . '.ics"');

    echo $ics;
    exit;
});

function fcmanager_build_ics($team, $matches)
{
    $domain    = parse_url(home_url(), PHP_URL_HOST);
    $tz_string = get_option('timezone_string') ?: 'Europe/Amsterdam';
    $tz        = new DateTimeZone($tz_string);

    $lines = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//FOOTBALL CLUB MANAGER//Matches//EN',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'X-WR-TIMEZONE:' . $tz_string,
        'X-WR-CALNAME:' . fcmanager_ics_escape($team->post_title),
    ];

    foreach ($matches as $match) {
        $uid = 'match-' . $match->ID . '@' . $domain;

        $date = get_post_meta($match->ID, '_fcmanager_match_date', true);
        $start_time = $date . ' ' . get_post_meta($match->ID, '_fcmanager_match_starttime', true);
        $end_time = get_post_meta($match->ID, '_fcmanager_match_endtime', true);
        if (empty($end_time)) {
            $end_time_dt = new DateTime($start_time);
            $end_time_dt->modify('+2 hours');
            $end_time = $end_time_dt->format('Y-m-d H:i');
        } else {
            $end_time = $date . ' ' . $end_time;
        }

        $sequence = get_post_modified_time('U', true, $match->ID);

        $lines[] = 'BEGIN:VEVENT';
        $lines[] = 'UID:' . $uid;
        $lines[] = 'DTSTART;TZID=' . $tz_string . ':' . fcmanager_ics_datetime($start_time, $tz);
        $lines[] = 'DTEND;TZID='   . $tz_string . ':' . fcmanager_ics_datetime($end_time, $tz);
        $lines[] = 'SEQUENCE:' . $sequence;
        $lines[] = 'SUMMARY:' . fcmanager_ics_escape(fcmanager_get_home_team_name($match->ID) . ' - ' . fcmanager_get_away_team_name($match->ID));
        $lines[] = 'END:VEVENT';
    }

    $lines[] = 'END:VCALENDAR';

    return implode("\r\n", $lines);
}

function fcmanager_ics_escape($text)
{
    return preg_replace('/([\,;])/', '\\\\$1', $text);
}

function fcmanager_ics_datetime($datetime_string, DateTimeZone $tz)
{
    $dt = new DateTime($datetime_string, $tz);
    return $dt->format('Ymd\THis');
}
