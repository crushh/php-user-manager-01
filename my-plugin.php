<?php
/**
 * Plugin Name: php-user-manager-01
 * Plugin URI: https://example.com/my-plugin
 * Description: 用户管理系统
 * Version: 1.0.0
 * Author: Zoe
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: my-plugin
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('MY_PLUGIN_VERSION', '1.0.0'); // 插件版本号
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__)); // 获取插件的绝对服务器路径
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__)); // 获取插件的绝对 URL


/***
 *  使用spl_autoload_register注册自动加载函数
    设置命名空间前缀 AstarUM\\
    检查类名是否以指定前缀开头
    转换类名为文件名：
    转换为小写
    下划线转为连字符
    添加 class- 前缀
    构建完整的文件路径
    如果文件存在则加载，否则记录错误日志
 */
spl_autoload_register(function ($class) {
    $prefix = 'AstarUM\\';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        error_log("Class {$class} does not match prefix {$prefix}");
        return;
    }

    $relative_class = substr($class, $len);

    $filename = 'class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';
    $file = MY_PLUGIN_PATH . 'includes/' . $filename;

    if (file_exists($file)) {
        error_log("File found, requiring: " . $file);
        require $file;
    } else {
        error_log("File not found: " . $file);
    }
});

// Initialize the plugin
function astar_um_init() {
    new AstarUM\User_Roles();
}

add_action('plugins_loaded', 'astar_um_init'); //plugins_loaded： Run when WordPress is ready
 
 

// 注册停用钩子
register_deactivation_hook(__FILE__, 'astar_um_deactivate');

function astar_um_deactivate() {
    // 1. 删除自定义角色
    $custom_roles = [
        'aupair',
        'family',
        'consultant'
    ];

    foreach ($custom_roles as $role) {
        remove_role($role);
    }

    // 2. 删除自定义权限
    $custom_capabilities = [
        'view_assigned_users',
        'access_assigned_list',
        'show_admin_bar',
        'view_admin_dashboard'
    ];

    // 获取所有角色并移除自定义权限
    global $wp_roles;
    if (!isset($wp_roles)) {
        wp_roles();
    }

    foreach ($wp_roles->roles as $role_name => $role_info) {
        $role = get_role($role_name);
        if ($role) {
            foreach ($custom_capabilities as $cap) {
                $role->remove_cap($cap);
            }
        }
    }

    // 3. 清理用户元数据
    global $wpdb;
    $meta_keys_to_delete = [
        'assigned_users',
        'astar_um_user_type',
        'astar_um_custom_permissions'
    ];

    foreach ($meta_keys_to_delete as $meta_key) {
        $wpdb->delete(
            $wpdb->usermeta,
            ['meta_key' => $meta_key]
        );
    }

    // 4. 删除插件相关选项
    delete_option('astar_um_version');
    delete_option('astar_um_install_date');
    delete_option('astar_um_settings');

    // 5. 清理临时数据和缓存
    delete_transient('astar_um_cache_key');

    // 6. 可选：日志记录
    error_log('Astar User Management Plugin Deactivated: ' . date('Y-m-d H:i:s'));
}