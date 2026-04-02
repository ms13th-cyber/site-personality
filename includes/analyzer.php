<?php

function spa_calculate_score() {

    $score = 100;

    // 投稿数
    $post_count = wp_count_posts()->publish;
    if ($post_count < 10) $score -= 20;

    // 更新頻度
    $latest = get_posts(['numberposts' => 1]);
    if ($latest) {
        $days = (time() - strtotime($latest[0]->post_date)) / 86400;
        if ($days > 30) $score -= 20;
    }

    // 内部リンク
    $posts = get_posts(['numberposts' => 5]);
    $link_count = 0;

    foreach ($posts as $p) {
        $link_count += substr_count($p->post_content, 'href');
    }

    if ($link_count < 5) $score -= 20;

    return max($score, 0);
}

function spa_google_rating($score) {
    if ($score >= 85) return 'S';
    if ($score >= 70) return 'A';
    if ($score >= 50) return 'B';
    if ($score >= 30) return 'C';
    return 'D';
}

function spa_get_issues() {

    $issues = [];

    if (wp_count_posts()->publish < 10) {
        $issues[] = '記事数が少ない';
    }

    $latest = get_posts(['numberposts' => 1]);
    if ($latest) {
        $days = (time() - strtotime($latest[0]->post_date)) / 86400;
        if ($days > 30) {
            $issues[] = '更新が止まってる';
        }
    }

    if (empty($issues)) {
        $issues[] = '大きな問題はなし👍';
    }

    return $issues;
}