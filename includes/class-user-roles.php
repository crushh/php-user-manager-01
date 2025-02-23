<?php
namespace AstarUM;

class User_Roles {
    private $roles = [
        'aupair' => [
            'name' => '学生',
            'capabilities' => [
                'read' => true, // 阅读文章的基本权限（内置权限）
                'edit_profile' => true,// 编辑个人资料（内置权限）
                'show_admin_bar' => false, // 自定义：显示管理工具栏
                'view_admin_dashboard' => false// 自定义：查看后台仪表盘
            ]
        ],
        'family' => [
            'name' => '家庭',
            'capabilities' => [
                'read' => true,
                'edit_profile' => true,
                'show_admin_bar' => false,
                'view_admin_dashboard' => false
            ]
        ],
        'consultant' => [
            'name' => '老师',
            'capabilities' => [
                'read' => true,
                'edit_profile' => true,
                'view_assigned_users' => true, // 自定义：查看指定用户
                'access_assigned_list' => true,// 自定义：访问指定列表
                'edit_pages' => false, // 编辑页面（内置权限）
                'edit_posts' => false, // 编辑文章（内置权限）
                'list_users' => true, // 查看用户列表（内置权限）
                'edit_users' => false,   // 编辑用户（内置权限）
                'view_admin_dashboard' => true   // 自定义：查看后台仪表盘
            ]
        ]
    ];

    public function __construct() {
        add_action('init', [$this, 'register_roles']);//当 WordPress 初始化时，调用这个类的 register_roles 方法
    }

    public function register_roles() {
        foreach ($this->roles as $role_key => $role) {
            add_role($role_key, $role['name'], $role['capabilities']); 
            //add_role是WordPress函数，用于添加角色，参数：
            // $role_key: 角色标识符
            // $role['name']: 角色显示名称
            // $role['capabilities']: 角色权限数组
        }
    }
 
}