<?php

/**
 * Role-based navigation: grouped sections for the sidebar (and flattened for minimal shells).
 *
 * Authenticated app URLs are unified under /dashboard and /dashboard/{slug}, e.g.:
 * - /dashboard — role home (exam officers see a dedicated home; registry is /dashboard/registry)
 * - /dashboard/submissions — lecturer
 * - /dashboard/department — HOD assignments; /dashboard/approvals — HOD queue
 * - /dashboard/reviews — moderator
 * - /dashboard/registry — exam officer
 * - /dashboard/search — global search (header form)
 *
 * Legacy /dashboard/admin/... and /lecturer, /hod, /moderator, /admin, /exam-officer paths 301-redirect where applicable.
 *
 * Item keys:
 * - label, route, pattern, pattern_unless, icon
 * - disabled: if true, no route — rendered as non-interactive "coming soon"
 */
return [
    'admin' => [
        [
            'group' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'grid'],
            ],
        ],
        [
            'group' => 'Management',
            'items' => [
                ['label' => 'User Management', 'route' => 'dashboard.users.index', 'pattern' => 'dashboard.users.*', 'icon' => 'users'],
                ['label' => 'Universities', 'route' => 'dashboard.universities.index', 'pattern' => 'dashboard.universities.*', 'icon' => 'building'],
                ['label' => 'Faculties', 'route' => 'dashboard.faculties.index', 'pattern' => 'dashboard.faculties.*', 'icon' => 'layers'],
                ['label' => 'Departments', 'route' => 'dashboard.departments.index', 'pattern' => 'dashboard.departments.*', 'icon' => 'map-pin'],
                ['label' => 'System Roles', 'route' => 'dashboard.roles.index', 'pattern' => 'dashboard.roles.*', 'icon' => 'shield'],
            ],
        ],
        [
            'group' => 'Security',
            'items' => [
                ['label' => 'Activity Logs', 'route' => 'dashboard.activity-logs.index', 'pattern' => 'dashboard.activity-logs.*', 'icon' => 'activity'],
                ['label' => 'Blocked Users', 'route' => 'dashboard.blocked-users.index', 'pattern' => 'dashboard.blocked-users.*', 'icon' => 'ban'],
            ],
        ],
        [
            'group' => 'Settings',
            'items' => [
                ['label' => 'System Settings', 'route' => 'dashboard.system.edit', 'pattern' => 'dashboard.system.*', 'icon' => 'cog'],
            ],
        ],
    ],

    'hod' => [
        [
            'group' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'grid'],
            ],
        ],
        [
            'group' => 'Submissions',
            'items' => [
                ['label' => 'Exam submissions & assignments', 'route' => 'dashboard.department.index', 'pattern' => 'dashboard.department.*', 'icon' => 'folder'],
                ['label' => 'Approval queue', 'route' => 'dashboard.approvals.index', 'pattern' => 'dashboard.approvals.*', 'icon' => 'check'],
                ['label' => 'User accounts', 'route' => 'dashboard.department.users.index', 'pattern' => 'dashboard.department.users.*', 'icon' => 'users'],
            ],
        ],
    ],

    'lecturer' => [
        [
            'group' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'grid'],
            ],
        ],
        [
            'group' => 'Submissions',
            'items' => [
                [
                    'label' => 'My Submissions',
                    'route' => 'dashboard.submissions.index',
                    'pattern' => 'dashboard.submissions.*',
                    'pattern_unless' => 'dashboard.submissions.create',
                    'icon' => 'folder',
                ],
                ['label' => 'Create Submission', 'route' => 'dashboard.submissions.create', 'pattern' => 'dashboard.submissions.create', 'icon' => 'plus'],
            ],
        ],
    ],

    'moderator' => [
        [
            'group' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'grid'],
            ],
        ],
        [
            'group' => 'Reviews',
            'items' => [
                [
                    'label' => 'Review Queue',
                    'route' => 'dashboard.reviews.index',
                    'pattern' => 'dashboard.reviews.*',
                    'icon' => 'clipboard',
                ],
            ],
        ],
    ],

    'exam_officer' => [
        [
            'group' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'grid'],
            ],
        ],
        [
            'group' => 'Exams',
            'items' => [
                [
                    'label' => 'Approved Exams',
                    'route' => 'dashboard.registry',
                    'pattern' => 'dashboard.registry',
                    'icon' => 'table',
                ],
                [
                    'label' => 'Search Records',
                    'route' => 'dashboard.search',
                    'pattern' => 'dashboard.search',
                    'icon' => 'search',
                ],
            ],
        ],
    ],
];
