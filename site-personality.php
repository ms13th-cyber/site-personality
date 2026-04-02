<?php
/*
Plugin Name: Site Personality Analyzer
Description: サイトを人格化＋Google視点で診断
Version: 1.0
Tested up to: 6.9.4
Requires PHP: 8.3.23
Author: masato shibuya(Image-box Co., Ltd.)
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/analyzer.php';
require_once plugin_dir_path(__FILE__) . 'includes/personality.php';

add_action('admin_menu', function() {
    add_menu_page(
        'サイト診断',
        'サイト診断',
        'manage_options',
        'site-personality',
        'spa_admin_page',
        'dashicons-chart-area',
        3
    );
});

add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_site-personality') return;

    wp_enqueue_style(
        'spa-style',
        plugin_dir_url(__FILE__) . 'assets/admin.css',
        [],
        '1.1'
    );
});

function spa_admin_page() {

    $score = spa_calculate_score();
    $personality = spa_get_personality($score);
    $comment = spa_random_comment($personality['type']);
    $google = spa_google_rating($score);
    $issues = spa_get_issues();

?>
<div class="wrap spa-box">

    <h1>🧠 サイト診断</h1>

    <!-- メイン -->
    <div class="spa-card spa-<?php echo esc_attr($personality['type']); ?>">

        <div class="spa-header">
            <?php echo esc_html($personality['emoji']); ?>
            <?php echo esc_html($personality['type']); ?>
        </div>

        <div class="spa-score">
            <?php echo esc_html($score); ?>点
        </div>

        <p><?php echo esc_html($personality['message']); ?></p>

        <div class="spa-comment">
            💬 <?php echo esc_html($comment); ?>
        </div>

    </div>

    <!-- Google視点 -->
    <div class="spa-card">
        <div class="spa-header">📊 Google視点</div>
        <div class="spa-google">
            評価：<?php echo esc_html($google); ?>
        </div>
    </div>

    <!-- 改善ポイント -->
    <div class="spa-card">
        <div class="spa-header">⚠ 改善ポイント</div>
        <ul class="spa-list">
            <?php foreach ($issues as $issue): ?>
                <li>・<?php echo esc_html($issue); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>
<?php
}


require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

$updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/ms13th-cyber/site-personality/',
    __FILE__,
    'site-personality'
);

$updateChecker->setBranch('main');