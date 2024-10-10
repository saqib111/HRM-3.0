@if (
        !Route::is(['chat', 'voice-call', 'video-call', 'outgoing-call', 'incoming-call', 'events', 'contacts', 'inbox', 'file-manager'])
    )
    <!-- Two Col Sidebar -->
    <div class="two-col-bar" id="two-col-bar">
        <div class="sidebar sidebar-twocol" id="navbar-nav">
            <div class="sidebar-left slimscroll">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link {{ Request::is('admin-dashboard', 'employee-dashboard', 'deals-dashboard', 'leads-dashboard') ? 'active' : '' }}"
                        id="v-pills-dashboard-tab" title="Dashboard" data-bs-toggle="pill" href="#v-pills-dashboard"
                        role="tab" aria-controls="v-pills-dashboard" aria-selected="true">
                        <span class="material-icons-outlined">
                            home
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-apps-tab" title="Apps" data-bs-toggle="pill" href="#v-pills-apps"
                        role="tab" aria-controls="v-pills-apps" aria-selected="false">
                        <span class="material-icons-outlined">
                            dashboard
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('employees-list', 'employees', 'holidays', 'leaves', 'leaves-employee', 'leave-settings', 'attendance', 'attendance-employee', 'departments', 'designations', 'timesheet', 'shift-scheduling', 'overtime', 'shift-list') ? 'active' : '' }}"
                        id="v-pills-employees-tab" title="Employees" data-bs-toggle="pill" href="#v-pills-employees"
                        role="tab" aria-controls="v-pills-employees" aria-selected="false">
                        <span class="material-icons-outlined">
                            people
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('clients', 'clients-list') ? 'active' : '' }}"
                        id="v-pills-clients-tab" title="Clients" data-bs-toggle="pill" href="#v-pills-clients" role="tab"
                        aria-controls="v-pills-clients" aria-selected="false">
                        <span class="material-icons-outlined">
                            person
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('projects', 'tasks', 'task-board', 'project-list') ? 'active' : '' }}"
                        id="v-pills-projects-tab" title="Projects" data-bs-toggle="pill" href="#v-pills-projects" role="tab"
                        aria-controls="v-pills-projects" aria-selected="false">
                        <span class="material-icons-outlined">
                            topic
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('contact-list', 'companies', 'deals', 'leads', 'leads-details', 'leads-kanban', 'pipeline', 'analytics', 'contact-grid', 'contact-details', 'companies-grid', 'company-details', 'deals-kanban', 'deals-details') ? 'active' : '' }}"
                        id="v-pills-leads-tab" title="CRM" data-bs-toggle="pill" href="#v-pills-leads" role="tab"
                        aria-controls="v-pills-leads" aria-selected="false">
                        <span class="material-icons-outlined">
                            leaderboard
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('tickets', 'ticket-details') ? 'active' : '' }}"
                        id="v-pills-tickets-tab" title="Tickets" data-bs-toggle="pill" href="#v-pills-tickets" role="tab"
                        aria-controls="v-pills-tickets" aria-selected="false">
                        <span class="material-icons-outlined">
                            confirmation_number
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('estimates', 'invoices', 'payments', 'expenses', 'provident-fund', 'taxes', 'edit-estimate', 'create-invoice', 'edit-invoice', 'create-estimate') ? 'active' : '' }}"
                        id="v-pills-sales-tab" title="Sales" data-bs-toggle="pill" href="#v-pills-sales" role="tab"
                        aria-controls="v-pills-sales" aria-selected="false">
                        <span class="material-icons-outlined">
                            shopping_bag
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('categories', 'budgets', 'budget-expenses', 'budget-revenues', 'sub-category') ? 'active' : '' }}"
                        id="v-pills-accounting-tab" title="Accounting" data-bs-toggle="pill" href="#v-pills-accounting"
                        role="tab" aria-controls="v-pills-accounting" aria-selected="false">
                        <span class="material-icons-outlined">
                            account_balance_wallet
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('salary', 'salary-view', 'payroll-items') ? 'active' : '' }}"
                        id="v-pills-payroll-tab" title="Payroll" data-bs-toggle="pill" href="#v-pills-payroll" role="tab"
                        aria-controls="v-pills-payroll" aria-selected="false">
                        <span class="material-icons-outlined">
                            request_quote
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('policies') ? 'active' : '' }}" id="v-pills-policies-tab"
                        title="Policies" data-bs-toggle="pill" href="#v-pills-policies" role="tab"
                        aria-controls="v-pills-policies" aria-selected="false">
                        <span class="material-icons-outlined">
                            verified_user
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('expense-reports', 'invoice-reports', 'payments-reports', 'task-reports', 'user-reports', 'employee-reports', 'payslip-reports', 'attendance-reports', 'leave-reports', 'daily-reports', 'project-reports') ? 'active' : '' }}"
                        id="v-pills-reports-tab" title="Reports" data-bs-toggle="pill" href="#v-pills-reports" role="tab"
                        aria-controls="v-pills-reports" aria-selected="false">
                        <span class="material-icons-outlined">
                            report_gmailerrorred
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('performance-indicator', 'performance', 'performance-appraisal') ? 'active' : '' }}"
                        id="v-pills-performance-tab" title="Performance" data-bs-toggle="pill" href="#v-pills-performance"
                        role="tab" aria-controls="v-pills-performance" aria-selected="false">
                        <span class="material-icons-outlined">
                            shutter_speed
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('goal-tracking', 'goal-type') ? 'active' : '' }}"
                        id="v-pills-goals-tab" title="Goals" data-bs-toggle="pill" href="#v-pills-goals" role="tab"
                        aria-controls="v-pills-goals" aria-selected="false">
                        <span class="material-icons-outlined">
                            track_changes
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('training', 'trainers', 'training-type') ? 'active' : '' }}"
                        id="v-pills-training-tab" title="Training" data-bs-toggle="pill" href="#v-pills-training" role="tab"
                        aria-controls="v-pills-training" aria-selected="false">
                        <span class="material-icons-outlined">
                            checklist_rtl
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('promotion') ? 'active' : '' }}" id="v-pills-promotion-tab"
                        title="Promotions" data-bs-toggle="pill" href="#v-pills-promotion" role="tab"
                        aria-controls="v-pills-promotion" aria-selected="false">
                        <span class="material-icons-outlined">
                            auto_graph
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('resignation') ? 'active' : '' }}" id="v-pills-resignation-tab"
                        title="Resignation" data-bs-toggle="pill" href="#v-pills-resignation" role="tab"
                        aria-controls="v-pills-resignation" aria-selected="false">
                        <span class="material-icons-outlined">
                            do_not_disturb_alt
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('termination') ? 'active' : '' }}" id="v-pills-termination-tab"
                        title="Termination" data-bs-toggle="pill" href="#v-pills-termination" role="tab"
                        aria-controls="v-pills-termination" aria-selected="false">
                        <span class="material-icons-outlined">
                            indeterminate_check_box
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('assets1') ? 'active' : '' }}" id="v-pills-assets-tab" title="Assets"
                        data-bs-toggle="pill" href="#v-pills-assets" role="tab" aria-controls="v-pills-assets"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            web_asset
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('user-dashboard', 'jobs-dashboard', 'jobs', 'job-applicants', 'manage-resumes', 'shortlist-candidates', 'interview-questions', 'offer_approvals', 'experiance-level', 'candidates', 'schedule-timing', 'apptitude-result', 'user-all-jobs', 'saved-jobs', 'applied-jobs', 'interviewing', 'offered-jobs', 'visited-jobs', 'archived-jobs', 'job-aptitude', 'questions') ? 'active' : '' }}"
                        id="v-pills-jobs-tab" title="Jobs" data-bs-toggle="pill" href="#v-pills-jobs" role="tab"
                        aria-controls="v-pills-jobs" aria-selected="false">
                        <span class="material-icons-outlined">
                            work_outline
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('knowledgebase', 'knowledgebase-view') ? 'active' : '' }} "
                        id="v-pills-knowledgebase-tab" title="Knowledgebase" data-bs-toggle="pill"
                        href="#v-pills-knowledgebase" role="tab" aria-controls="v-pills-knowledgebase"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            school
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('activities') ? 'active' : '' }} " id="v-pills-activities-tab"
                        title="Activities" data-bs-toggle="pill" href="#v-pills-activities" role="tab"
                        aria-controls="v-pills-activities" aria-selected="false">
                        <span class="material-icons-outlined">
                            toggle_off
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('users') ? 'active' : '' }}" id="v-pills-users-tab" title="Users"
                        data-bs-toggle="pill" href="#v-pills-users" role="tab" aria-controls="v-pills-users"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            group_add
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('settings', 'localization', 'theme-settings', 'roles-permissions', 'email-settings', 'performance-setting', 'approval-setting', 'invoice-settings', 'salary-settings', 'notifications-settings', 'change-password', 'leave-type', 'toxbox-setting', 'cron-setting') ? 'active' : '' }}"
                        id="v-pills-settings-tab" title="Settings" data-bs-toggle="pill" href="#v-pills-settings" role="tab"
                        aria-controls="v-pills-settings" aria-selected="false">
                        <span class="material-icons-outlined">
                            settings
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('profile', 'client-profile', 'user-asset-details') ? 'active' : '' }}"
                        id="v-pills-profile-tab" title="Profile" data-bs-toggle="pill" href="#v-pills-profile" role="tab"
                        aria-controls="v-pills-profile" aria-selected="false">
                        <span class="material-icons-outlined">
                            manage_accounts
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('index', 'register', 'forgot-password', 'otp', 'lock-screen') ? 'active' : '' }}"
                        id="v-pills-authentication-tab" title="Authentication" data-bs-toggle="pill"
                        href="#v-pills-authentication" role="tab" aria-controls="v-pills-authentication"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            perm_contact_calendar
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('error-404', 'error-500') ? 'active' : '' }}"
                        id="v-pills-errorpages-tab" title="Error Pages" data-bs-toggle="pill" href="#v-pills-errorpages"
                        role="tab" aria-controls="v-pills-errorpages" aria-selected="false">
                        <span class="material-icons-outlined">
                            announcement
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('subscriptions', 'subscriptions-company', 'subscribed-companies') ? 'active' : '' }}"
                        id="v-pills-subscriptions-tab" title="Subscriptions" data-bs-toggle="pill"
                        href="#v-pills-subscriptions" role="tab" aria-controls="v-pills-subscriptions"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            loyalty
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('search', 'faq', 'terms', 'privacy-policy', 'blank-page', 'coming-soon', 'under-maintenance') ? 'active' : '' }}"
                        id="v-pills-pages-tab" title="Pages" data-bs-toggle="pill" href="#v-pills-pages" role="tab"
                        aria-controls="v-pills-pages" aria-selected="false">
                        <span class="material-icons-outlined">
                            layers
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is(
            'ui-alerts',
            'ui-accordion',
            'ui-avatar',
            'ui-badges',
            'ui-borders',
            'ui-buttons',
            'ui-buttons-group',
            'ui-breadcrumb',
            'ui-cards',
            'ui-carousel',
            'ui-colors',
            'ui-dropdowns',
            'ui-grid',
            'ui-images',
            'ui-lightbox',
            'ui-media',
            'ui-modals',
            'ui-notification',
            'ui-offcanvas',
            'ui-pagination',
            'ui-popovers',
            'ui-progress',
            'ui-placeholders',
            'ui-rangeslider',
            'ui-spinner',
            'ui-sweetalerts',
            'ui-nav-tabs',
            'ui-toasts',
            'ui-tooltips',
            'ui-typography',
            'ui-video'
        ) ? 'active' : '' }}" id="v-pills-baseui-tab" title="Base UI" data-bs-toggle="pill" href="#v-pills-baseui"
                        role="tab" aria-controls="v-pills-baseui" aria-selected="false">
                        <span class="material-icons-outlined">
                            foundation
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('ui-ribbon', 'ui-clipboard', 'ui-drag-drop', 'ui-rangeslider', 'ui-rating', 'ui-text-editor', 'ui-counter', 'ui-scrollbar', 'ui-stickynote', 'ui-timeline') ? 'active' : '' }}"
                        id="v-pills-elements-tab" title="Advanced UI" data-bs-toggle="pill" href="#v-pills-elements"
                        role="tab" aria-controls="v-pills-elements" aria-selected="false">
                        <span class="material-icons-outlined">
                            bento
                        </span>
                    </a>
                    <a class="nav-link  {{ Request::is('chart-apex', 'chart-js', 'chart-morris', 'chart-flot', 'chart-peity', 'chart-c3') ? 'active' : '' }}"
                        id="v-pills-charts-tab" title="Charts" data-bs-toggle="pill" href="#v-pills-charts" role="tab"
                        aria-controls="v-pills-charts" aria-selected="false">
                        <span class="material-icons-outlined">
                            bar_chart
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('icon-fontawesome', 'icon-feather', 'icon-ionic', 'icon-material', 'icon-pe7', 'icon-simpleline', 'icon-themify', 'icon-weather', 'icon-typicon', 'icon-flag') ? 'active' : '' }}"
                        id="v-pills-icons-tab" title="Icons" data-bs-toggle="pill" href="#v-pills-icons" role="tab"
                        aria-controls="v-pills-icons" aria-selected="false">
                        <span class="material-icons-outlined">
                            grading
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('form-basic-inputs', 'form-input-groups', 'form-horizontal', 'form-vertical', 'form-mask', 'form-validation', 'form-select2', 'form-fileupload', 'horizontal-timeline', 'form-wizard') ? 'active' : '' }}"
                        id="v-pills-forms-tab" title="Forms" data-bs-toggle="pill" href="#v-pills-forms" role="tab"
                        aria-controls="v-pills-forms" aria-selected="false">
                        <span class="material-icons-outlined">
                            view_day
                        </span>
                    </a>
                    <a class="nav-link {{ Request::is('tables-basic', 'data-tables') ? 'active' : '' }}"
                        id="v-pills-tables-tab" title="Tables" data-bs-toggle="pill" href="#v-pills-tables" role="tab"
                        aria-controls="v-pills-tables" aria-selected="false">
                        <span class="material-icons-outlined">
                            table_rows
                        </span>
                    </a>
                    <a class="nav-link " id="v-pills-documentation-tab" title="Documentation" data-bs-toggle="pill"
                        href="#v-pills-documentation" role="tab" aria-controls="v-pills-documentation"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            description
                        </span>
                    </a>
                    <a class="nav-link " id="v-pills-changelog-tab" title="Changelog" data-bs-toggle="pill"
                        href="#v-pills-changelog" role="tab" aria-controls="v-pills-changelog" aria-selected="false">
                        <span class="material-icons-outlined">
                            sync_alt
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-multilevel-tab" title="Multilevel" data-bs-toggle="pill"
                        href="#v-pills-multilevel" role="tab" aria-controls="v-pills-multilevel" aria-selected="false">
                        <span class="material-icons-outlined">
                            library_add_check
                        </span>
                    </a>
                </div>
            </div>

            <div class="sidebar-right">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show  {{ Request::is('admin-dashboard', 'employee-dashboard', 'deals-dashboard', 'leads-dashboard') ? 'active' : '' }}"
                        id="v-pills-dashboard" role="tabpanel" aria-labelledby="v-pills-dashboard-tab">
                        <p>Dashboard</p>
                        <ul>
                            <li>
                                <a class="{{ Request::is('admin-dashboard') ? 'active' : '' }}"
                                    href="{{url('admin-dashboard')}}">Admin Dashboard</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('employee-dashboard') ? 'active' : '' }}"
                                    href="{{url('employee-dashboard')}}">Employee Dashboard</a>
                            </li>
                            <li><a class="{{ Request::is('deals-dashboard') ? 'active' : '' }}"
                                    href="{{url('deals-dashboard')}}">Deals Dashboard</a></li>
                            <li><a class="{{ Request::is('leads-dashboard') ? 'active' : '' }}"
                                    href="{{url('leads-dashboard')}}">Leads Dashboard</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-apps" role="tabpanel" aria-labelledby="v-pills-apps-tab">
                        <p>App</p>
                        <ul>
                            <li>
                                <a class="{{ Request::is('chat') ? 'active' : '' }}" href="{{url('chat')}}">Chat</a>
                            </li>
                            <li class="sub-menu">
                                <a href="#">Calls <span class="menu-arrow"></span></a>
                                <ul>
                                    <li><a class="{{ Request::is('voice-call') ? 'active' : '' }}"
                                            href="{{url('voice-call')}}">Voice Call</a></li>
                                    <li><a class="{{ Request::is('video-call') ? 'active' : '' }}"
                                            href="{{url('video-call')}}">Video Call</a></li>
                                    <li><a class="{{ Request::is('outgoing-call') ? 'active' : '' }}"
                                            href="{{url('outgoing-call')}}">Outgoing Call</a></li>
                                    <li><a class="{{ Request::is('incoming-call') ? 'active' : '' }}"
                                            href="{{url('incoming-call')}}">Incoming Call</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="{{ Request::is('events') ? 'active' : '' }}" href="{{url('events')}}">Calendar</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('contacts') ? 'active' : '' }}"
                                    href="{{url('contacts')}}">Contacts</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('inbox') ? 'active' : '' }}" href="{{url('inbox')}}">Email</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('file-manager') ? 'active' : '' }}"
                                    href="{{url('file-manager')}}">File Manager</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('employees-list', 'employees', 'holidays', 'leaves', 'leaves-employee', 'leave-settings', 'attendance', 'attendance-employee', 'departments', 'designations', 'timesheet', 'shift-scheduling', 'overtime', 'shift-list') ? 'active' : '' }}"
                        id="v-pills-employees" role="tabpanel" aria-labelledby="v-pills-employees-tab">
                        <p>Employees</p>
                        <ul>
                            <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                    href="{{url('employees')}}">All Employees</a></li>
                            <li><a class="{{ Request::is('holidays') ? 'active' : '' }}"
                                    href="{{url('holidays')}}">Holidays</a></li>
                            <li><a class="{{ Request::is('leaves') ? 'active' : '' }}" href="{{url('leaves')}}">Leaves
                                    (Admin) <span class="badge rounded-pill bg-primary float-end">1</span></a></li>
                            <li><a class="{{ Request::is('leaves-employee') ? 'active' : '' }}"
                                    href="{{url('leaves-employee')}}">Leaves (Employee)</a></li>
                            <li><a class="{{ Request::is('leave-settings') ? 'active' : '' }}"
                                    href="{{url('leave-settings')}}">Leave Settings</a></li>
                            <li><a class="{{ Request::is('attendance') ? 'active' : '' }}"
                                    href="{{url('attendance')}}">Attendance (Admin)</a></li>
                            <li><a class="{{ Request::is('attendance-employee') ? 'active' : '' }}"
                                    href="{{url('attendance-employee')}}">Attendance (Employee)</a></li>
                            <li><a class="{{ Request::is('departments') ? 'active' : '' }}"
                                    href="{{url('departments')}}">Departments</a></li>
                            <li><a class="{{ Request::is('designations') ? 'active' : '' }}"
                                    href="{{url('designations')}}">Designations</a></li>
                            <li><a class="{{ Request::is('timesheet') ? 'active' : '' }}"
                                    href="{{url('timesheet')}}">Timesheet</a></li>
                            <li><a class="{{ Request::is('shift-scheduling', 'shift-list') ? 'active' : '' }}"
                                    href="{{url('shift-scheduling')}}">Shift & Schedule</a></li>
                            <li><a class="{{ Request::is('overtime') ? 'active' : '' }}"
                                    href="{{url('overtime')}}">Overtime</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('clients', 'clients-list') ? 'active' : '' }}"
                        id="v-pills-clients" role="tabpanel" aria-labelledby="v-pills-clients-tab">
                        <p>Clients</p>
                        <ul>
                            <li><a class="{{ Request::is('clients', 'clients-list') ? 'active' : '' }}"
                                    href="{{url('clients')}}">Clients</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('projects', 'tasks', 'task-board', 'project-list') ? 'active' : '' }}"
                        id="v-pills-projects" role="tabpanel" aria-labelledby="v-pills-projects-tab">
                        <p>Projects</p>
                        <ul>
                            <li><a class="{{ Request::is('projects', 'project-list') ? 'active' : '' }}"
                                    href="{{url('projects')}}">Projects</a></li>
                            <li><a class="{{ Request::is('tasks') ? 'active' : '' }}" href="{{url('tasks')}}">Tasks</a></li>
                            <li><a class="{{ Request::is('task-board') ? 'active' : '' }}" href="{{url('task-board')}}">Task
                                    Board</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('contact-list', 'companies', 'deals', 'leads', 'leads-details', 'leads-kanban', 'pipeline', 'analytics', 'contact-grid', 'contact-details', 'companies-grid', 'company-details', 'deals-kanban', 'deals-details') ? 'active' : '' }}"
                        id="v-pills-leads" role="tabpanel" aria-labelledby="v-pills-leads-tab">
                        <p>CRM</p>
                        <ul>
                            <li>
                                <a class="{{ Request::is('contact-list', 'contact-grid', 'contact-details') ? 'active' : '' }}"
                                    href="{{url('contact-list')}}"> Contacts</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('companies', 'companies-grid', 'company-details') ? 'active' : '' }}"
                                    href="{{url('companies')}}">Companies</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('deals', 'deals-kanban', 'deals-details') ? 'active' : '' }}"
                                    href="{{url('deals')}}"> Deals</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('leads', 'leads-details', 'leads-kanban') ? 'active' : '' }}"
                                    href="{{url('leads')}}"> Leads </a>
                            </li>
                            <li>
                                <a class="{{ Request::is('pipeline') ? 'active' : '' }}" href="{{url('pipeline')}}">Pipeline
                                </a>
                            </li>
                            <li>
                                <a class="{{ Request::is('analytics') ? 'active' : '' }}"
                                    href="{{url('analytics')}}">Analytics</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('tickets', 'ticket-details') ? 'active' : '' }}"
                        id="v-pills-tickets" role="tabpanel" aria-labelledby="v-pills-tickets-tab">
                        <p>Tickets</p>
                        <ul>
                            <li><a class="{{ Request::is('tickets') ? 'active' : '' }}"
                                    href="{{url('tickets')}}">Tickets</a></li>
                            <li><a class="{{ Request::is('ticket-details') ? 'active' : '' }}"
                                    href="{{url('ticket-details')}}">Ticket Details</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('estimates', 'invoices', 'payments', 'expenses', 'provident-fund', 'taxes', 'edit-estimate', 'create-invoice', 'edit-invoice', 'create-estimate') ? 'active' : '' }}"
                        id="v-pills-sales" role="tabpanel" aria-labelledby="v-pills-sales-tab">
                        <p>Sales</p>
                        <ul>
                            <li><a class="{{ Request::is('estimates', 'edit-estimate', 'create-estimate') ? 'active' : '' }}"
                                    href="{{url('estimates')}}">Estimates</a></li>
                            <li><a class="{{ Request::is('invoices', 'create-invoice', 'edit-invoice') ? 'active' : '' }}"
                                    href="{{url('invoices')}}">Invoices</a></li>
                            <li><a class="{{ Request::is('payments') ? 'active' : '' }}"
                                    href="{{url('payments')}}">Payments</a></li>
                            <li><a class="{{ Request::is('expenses') ? 'active' : '' }}"
                                    href="{{url('expenses')}}">Expenses</a></li>
                            <li><a class="{{ Request::is('provident-fund') ? 'active' : '' }}"
                                    href="{{url('provident-fund')}}">Provident Fund</a></li>
                            <li><a class="{{ Request::is('taxes') ? 'active' : '' }}" href="{{url('taxes')}}">Taxes</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('categories', 'budgets', 'budget-expenses', 'budget-revenues', 'sub-category') ? 'active' : '' }}"
                        id="v-pills-accounting" role="tabpanel" aria-labelledby="v-pills-accounting-tab">
                        <p>Accounting</p>
                        <ul>
                            <li><a class="{{ Request::is('categories', 'sub-category') ? 'active' : '' }}"
                                    href="{{url('categories')}}">Categories</a></li>
                            <li><a class="{{ Request::is('budgets') ? 'active' : '' }}"
                                    href="{{url('budgets')}}">Budgets</a></li>
                            <li><a class="{{ Request::is('budget-expenses') ? 'active' : '' }}"
                                    href="{{url('budget-expenses')}}">Budget Expenses</a></li>
                            <li><a class="{{ Request::is('budget-revenues') ? 'active' : '' }}"
                                    href="{{url('budget-revenues')}}">Budget Revenues</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('salary', 'salary-view', 'payroll-items') ? 'active' : '' }}"
                        id="v-pills-payroll" role="tabpanel" aria-labelledby="v-pills-payroll-tab">
                        <p>Payroll</p>
                        <ul>
                            <li><a class="{{ Request::is('salary') ? 'active' : '' }}" href="{{url('salary')}}"> Employee
                                    Salary </a></li>
                            <li><a class="{{ Request::is('salary-view') ? 'active' : '' }}" href="{{url('salary-view')}}">
                                    Payslip </a></li>
                            <li><a class="{{ Request::is('payroll-items') ? 'active' : '' }}"
                                    href="{{url('payroll-items')}}"> Payroll Items </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('policies') ? 'active' : '' }}" id="v-pills-policies"
                        role="tabpanel" aria-labelledby="v-pills-policies-tab">
                        <p>Policies</p>
                        <ul>
                            <li><a class="{{ Request::is('policies') ? 'active' : '' }}" href="{{url('policies')}}">
                                    Policies </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('expense-reports', 'invoice-reports', 'payments-reports', 'task-reports', 'user-reports', 'employee-reports', 'payslip-reports', 'attendance-reports', 'leave-reports', 'daily-reports', 'project-reports') ? 'active' : '' }}"
                        id="v-pills-reports" role="tabpanel" aria-labelledby="v-pills-reports-tab">
                        <p>Reports</p>
                        <ul>
                            <li><a class="{{ Request::is('expense-reports') ? 'active' : '' }}"
                                    href="{{url('expense-reports')}}"> Expense Report </a></li>
                            <li><a class="{{ Request::is('invoice-reports') ? 'active' : '' }}"
                                    href="{{url('invoice-reports')}}"> Invoice Report </a></li>
                            <li><a class="{{ Request::is('payments-reports') ? 'active' : '' }}"
                                    href="{{url('payments-reports')}}"> Payments Report </a></li>
                            <li><a class="{{ Request::is('project-reports') ? 'active' : '' }}"
                                    href="{{url('project-reports')}}"> Project Report </a></li>
                            <li><a class="{{ Request::is('task-reports') ? 'active' : '' }}" href="{{url('task-reports')}}">
                                    Task Report </a></li>
                            <li><a class="{{ Request::is('user-reports') ? 'active' : '' }}" href="{{url('user-reports')}}">
                                    User Report </a></li>
                            <li><a class="{{ Request::is('employee-reports') ? 'active' : '' }}"
                                    href="{{url('employee-reports')}}"> Employee Report </a></li>
                            <li><a class="{{ Request::is('payslip-reports') ? 'active' : '' }}"
                                    href="{{url('payslip-reports')}}"> Payslip Report </a></li>
                            <li><a class="{{ Request::is('attendance-reports') ? 'active' : '' }}"
                                    href="{{url('attendance-reports')}}"> Attendance Report </a></li>
                            <li><a class="{{ Request::is('leave-reports') ? 'active' : '' }}"
                                    href="{{url('leave-reports')}}"> Leave Report </a></li>
                            <li><a class="{{ Request::is('daily-reports') ? 'active' : '' }}"
                                    href="{{url('daily-reports')}}"> Daily Report </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('performance-indicator', 'performance', 'performance-appraisal') ? 'active' : '' }}"
                        id="v-pills-performance" role="tabpanel" aria-labelledby="v-pills-performance-tab">
                        <p>Performance</p>
                        <ul>
                            <li><a class="{{ Request::is('performance-indicator') ? 'active' : '' }}"
                                    href="{{url('performance-indicator')}}"> Performance Indicator </a></li>
                            <li><a class="{{ Request::is('performance') ? 'active' : '' }}" href="{{url('performance')}}">
                                    Performance Review </a></li>
                            <li><a class="{{ Request::is('performance-appraisal') ? 'active' : '' }}"
                                    href="{{url('performance-appraisal')}}"> Performance Appraisal </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('goal-tracking', 'goal-type') ? 'active' : '' }}"
                        id="v-pills-goals" role="tabpanel" aria-labelledby="v-pills-goals-tab">
                        <p>Goals</p>
                        <ul>
                            <li><a class="{{ Request::is('goal-tracking') ? 'active' : '' }}"
                                    href="{{url('goal-tracking')}}"> Goal List </a></li>
                            <li><a class="{{ Request::is('goal-type') ? 'active' : '' }}" href="{{url('goal-type')}}"> Goal
                                    Type </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('training', 'trainers', 'training-type') ? 'active' : '' }} "
                        id="v-pills-training" role="tabpanel" aria-labelledby="v-pills-training-tab">
                        <p>Training</p>
                        <ul>
                            <li><a class="{{ Request::is('training') ? 'active' : '' }}" href="{{url('training')}}">
                                    Training List </a></li>
                            <li><a class="{{ Request::is('trainers') ? 'active' : '' }}" href="{{url('trainers')}}">
                                    Trainers</a></li>
                            <li><a class="{{ Request::is('training-type') ? 'active' : '' }}"
                                    href="{{url('training-type')}}"> Training Type </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('promotion') ? 'active' : '' }}" id="v-pills-promotion"
                        role="tabpanel" aria-labelledby="v-pills-promotion-tab">
                        <p>Promotion</p>
                        <ul>
                            <li><a class="{{ Request::is('promotion') ? 'active' : '' }}" href="{{url('promotion')}}">
                                    Promotion </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('resignation') ? 'active' : '' }}"
                        id="v-pills-resignation" role="tabpanel" aria-labelledby="v-pills-resignation-tab">
                        <p>Resignation</p>
                        <ul>
                            <li><a class="{{ Request::is('resignation') ? 'active' : '' }}" href="{{url('resignation')}}">
                                    Resignation </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('termination') ? 'active' : '' }}"
                        id="v-pills-termination" role="tabpanel" aria-labelledby="v-pills-termination-tab">
                        <p>Termination</p>
                        <ul>
                            <li><a class="{{ Request::is('termination') ? 'active' : '' }}" href="{{url('termination')}}">
                                    Termination </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('assets1') ? 'active' : '' }} " id="v-pills-assets"
                        role="tabpanel" aria-labelledby="v-pills-assets-tab">
                        <p>Assets</p>
                        <ul>
                            <li><a class="{{ Request::is('assets1') ? 'active' : '' }}" href="{{url('assets1')}}"> Assets
                                </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('user-dashboard', 'jobs-dashboard', 'jobs', 'job-applicants', 'manage-resumes', 'shortlist-candidates', 'interview-questions', 'offer_approvals', 'experiance-level', 'candidates', 'schedule-timing', 'apptitude-result', 'user-all-jobs', 'saved-jobs', 'applied-jobs', 'interviewing', 'offered-jobs', 'visited-jobs', 'archived-jobs', 'job-aptitude', 'questions') ? 'active' : '' }} "
                        id="v-pills-jobs" role="tabpanel" aria-labelledby="v-pills-jobs-tab">
                        <p>Jobs</p>
                        <ul>
                            <li><a class="{{ Request::is('user-dashboard', 'user-all-jobs', 'saved-jobs', 'applied-jobs', 'interviewing', 'offered-jobs', 'visited-jobs', 'archived-jobs', 'job-aptitude', 'questions') ? 'active' : '' }}"
                                    href="{{url('user-dashboard')}}" class="active"> User Dasboard </a></li>
                            <li><a class="{{ Request::is('jobs-dashboard') ? 'active' : '' }}"
                                    href="{{url('jobs-dashboard')}}"> Jobs Dasboard </a></li>
                            <li><a class="{{ Request::is('jobs') ? 'active' : '' }}" href="{{url('jobs')}}"> Manage Jobs
                                </a></li>
                            <li><a class="{{ Request::is('job-applicants') ? 'active' : '' }}"
                                    href="{{url('job-applicants')}}"> Applied Jobs </a></li>
                            <li><a class="{{ Request::is('manage-resumes') ? 'active' : '' }}"
                                    href="{{url('manage-resumes')}}"> Manage Resumes </a></li>
                            <li><a class="{{ Request::is('shortlist-candidates') ? 'active' : '' }}"
                                    href="{{url('shortlist-candidates')}}"> Shortlist Candidates </a></li>
                            <li><a class="{{ Request::is('interview-questions') ? 'active' : '' }}"
                                    href="{{url('interview-questions')}}"> Interview Questions </a></li>
                            <li><a class="{{ Request::is('offer_approvals') ? 'active' : '' }}"
                                    href="{{url('offer_approvals')}}"> Offer Approvals </a></li>
                            <li><a class="{{ Request::is('experiance-level') ? 'active' : '' }}"
                                    href="{{url('experiance-level')}}"> Experience Level </a></li>
                            <li><a class="{{ Request::is('candidates') ? 'active' : '' }}" href="{{url('candidates')}}">
                                    Candidates List </a></li>
                            <li><a class="{{ Request::is('schedule-timing') ? 'active' : '' }}"
                                    href="{{url('schedule-timing')}}"> Schedule timing </a></li>
                            <li><a class="{{ Request::is('apptitude-result') ? 'active' : '' }}"
                                    href="{{url('apptitude-result')}}"> Aptitude Results </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('knowledgebase', 'knowledgebase-view') ? 'active' : '' }}"
                        id="v-pills-knowledgebase" role="tabpanel" aria-labelledby="v-pills-knowledgebase-tab">
                        <p>Knowledgebase</p>
                        <ul>
                            <li><a class="{{ Request::is('knowledgebase', 'knowledgebase-view') ? 'active' : '' }}"
                                    href="{{url('knowledgebase')}}"> Knowledgebase </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('activities') ? 'active' : '' }}" id="v-pills-activities"
                        role="tabpanel" aria-labelledby="v-pills-activities-tab">
                        <p>Activities</p>
                        <ul>
                            <li><a class="{{ Request::is('activities') ? 'active' : '' }}" href="{{url('activities')}}">
                                    Activities </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('users') ? 'active' : '' }}" id="v-pills-users"
                        role="tabpanel" aria-labelledby="v-pills-activities-tab">
                        <p>Users</p>
                        <ul>
                            <li><a class="{{ Request::is('users') ? 'active' : '' }}" href="{{url('users')}}"> Users </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('settings', 'localization', 'theme-settings', 'roles-permissions', 'email-settings', 'performance-setting', 'approval-setting', 'invoice-settings', 'salary-settings', 'notifications-settings', 'change-password', 'leave-type', 'toxbox-setting', 'cron-setting') ? 'active' : '' }}"
                        id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                        <p>Settings</p>
                        <ul>
                            <li><a class="{{ Request::is('settings', 'localization', 'theme-settings', 'roles-permissions', 'email-settings', 'performance-setting', 'approval-setting', 'invoice-settings', 'salary-settings', 'notifications-settings', 'change-password', 'leave-type', 'toxbox-setting', 'cron-setting') ? 'active' : '' }}"
                                    href="{{url('settings')}}"> Settings </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('profile', 'client-profile', 'user-asset-details') ? 'active' : '' }}"
                        id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        <p>Profile</p>
                        <ul>
                            <li><a class="{{ Request::is('profile', 'user-asset-details') ? 'active' : '' }}"
                                    href="{{url('profile')}}"> Employee Profile </a></li>
                            <li><a class="{{ Request::is('client-profile') ? 'active' : '' }}"
                                    href="{{url('client-profile')}}"> Client Profile </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('index', 'register', 'forgot-password', 'otp', 'lock-screen') ? 'active' : '' }} "
                        id="v-pills-authentication" role="tabpanel" aria-labelledby="v-pills-authentication-tab">
                        <p>Authentication</p>
                        <ul>
                            <li><a class="{{ Request::is('index') ? 'active' : '' }}" href="{{url('index')}}"> Login </a>
                            </li>
                            <li><a class="{{ Request::is('register') ? 'active' : '' }}" href="{{url('register')}}">
                                    Register </a></li>
                            <li><a class="{{ Request::is('forgot-password') ? 'active' : '' }}"
                                    href="{{url('forgot-password')}}"> Forgot Password </a></li>
                            <li><a class="{{ Request::is('otp') ? 'active' : '' }}" href="{{url('otp')}}"> OTP </a></li>
                            <li><a class="{{ Request::is('lock-screen') ? 'active' : '' }}" href="{{url('lock-screen')}}">
                                    Lock Screen </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('error-404', 'error-500') ? 'active' : '' }}"
                        id="v-pills-errorpages" role="tabpanel" aria-labelledby="v-pills-errorpages-tab">
                        <p>Error Pages</p>
                        <ul>
                            <li><a class="{{ Request::is('error-404') ? 'active' : '' }}" href="{{url('error-404')}}">404
                                    Error </a></li>
                            <li><a class="{{ Request::is('error-500') ? 'active' : '' }}" href="{{url('error-500')}}">500
                                    Error </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('subscriptions', 'subscriptions-company', 'subscribed-companies') ? 'active' : '' }}"
                        id="v-pills-subscriptions" role="tabpanel" aria-labelledby="v-pills-subscriptions-tab">
                        <p>Subscriptions</p>
                        <ul>
                            <li><a class="{{ Request::is('subscriptions') ? 'active' : '' }}"
                                    href="{{url('subscriptions')}}"> Subscriptions (Admin) </a></li>
                            <li><a class="{{ Request::is('subscriptions-company') ? 'active' : '' }}"
                                    href="{{url('subscriptions-company')}}"> Subscriptions (Company) </a></li>
                            <li><a class="{{ Request::is('subscribed-companies') ? 'active' : '' }}"
                                    href="{{url('subscribed-companies')}}"> Subscribed Companies</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('search', 'faq', 'terms', 'privacy-policy', 'blank-page', 'coming-soon', 'under-maintenance') ? 'active' : '' }}"
                        id="v-pills-pages" role="tabpanel" aria-labelledby="v-pills-pages-tab">
                        <p>Pages</p>
                        <ul>
                            <li><a class="{{ Request::is('search') ? 'active' : '' }}" href="{{url('search')}}"> Search </a>
                            </li>
                            <li><a class="{{ Request::is('faq') ? 'active' : '' }}" href="{{url('faq')}}"> FAQ </a></li>
                            <li><a class="{{ Request::is('terms') ? 'active' : '' }}" href="{{url('terms')}}"> Terms </a>
                            </li>
                            <li><a class="{{ Request::is('privacy-policy') ? 'active' : '' }}"
                                    href="{{url('privacy-policy')}}"> Privacy Policy </a></li>
                            <li><a class="{{ Request::is('blank-page') ? 'active' : '' }}" href="{{url('blank-page')}}">
                                    Blank Page </a></li>
                            <li><a class="{{ Request::is('coming-soon') ? 'active' : '' }}" href="{{url('coming-soon')}}">
                                    Coming Soon </a></li>
                            <li><a class="{{ Request::is('under-maintenance') ? 'active' : '' }}"
                                    href="{{url('under-maintenance')}}"> Under Maintanance </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is(
            'ui-alerts',
            'ui-accordion',
            'ui-avatar',
            'ui-badges',
            'ui-borders',
            'ui-buttons',
            'ui-buttons-group',
            'ui-breadcrumb',
            'ui-cards',
            'ui-carousel',
            'ui-colors',
            'ui-dropdowns',
            'ui-grid',
            'ui-images',
            'ui-lightbox',
            'ui-media',
            'ui-modals',
            'ui-notification',
            'ui-offcanvas',
            'ui-pagination',
            'ui-popovers',
            'ui-progress',
            'ui-placeholders',
            'ui-rangeslider',
            'ui-spinner',
            'ui-sweetalerts',
            'ui-nav-tabs',
            'ui-toasts',
            'ui-tooltips',
            'ui-typography',
            'ui-video'
        ) ? 'active' : '' }} " id="v-pills-baseui" role="tabpanel" aria-labelledby="v-pills-baseui-tab">
                        <p>Base UI</p>
                        <ul>
                            <li><a class="{{ Request::is('ui-alerts') ? 'active' : '' }}"
                                    href="{{url('ui-alerts')}}">Alerts</a></li>
                            <li><a class="{{ Request::is('ui-accordion') ? 'active' : '' }}"
                                    href="{{url('ui-accordion')}}">Accordion</a></li>
                            <li><a class="{{ Request::is('ui-avatar') ? 'active' : '' }}"
                                    href="{{url('ui-avatar')}}">Avatar</a></li>
                            <li><a class="{{ Request::is('ui-badges') ? 'active' : '' }}"
                                    href="{{url('ui-badges')}}">Badges</a></li>
                            <li><a class="{{ Request::is('ui-borders') ? 'active' : '' }}"
                                    href="{{url('ui-borders')}}">Border</a></li>
                            <li><a class="{{ Request::is('ui-buttons') ? 'active' : '' }}"
                                    href="{{url('ui-buttons')}}">Buttons</a></li>
                            <li><a class="{{ Request::is('ui-buttons-group') ? 'active' : '' }}"
                                    href="{{url('ui-buttons-group')}}">Button Group</a></li>
                            <li><a class="{{ Request::is('ui-breadcrumb') ? 'active' : '' }}"
                                    href="{{url('ui-breadcrumb')}}">Breadcrumb</a></li>
                            <li><a class="{{ Request::is('ui-cards') ? 'active' : '' }}" href="{{url('ui-cards')}}">Card</a>
                            </li>
                            <li><a class="{{ Request::is('ui-carousel') ? 'active' : '' }}"
                                    href="{{url('ui-carousel')}}">Carousel</a></li>
                            <li><a class="{{ Request::is('ui-colors') ? 'active' : '' }}"
                                    href="{{url('ui-colors')}}">Colors</a></li>
                            <li><a class="{{ Request::is('ui-dropdowns') ? 'active' : '' }}"
                                    href="{{url('ui-dropdowns')}}">Dropdowns</a></li>
                            <li><a class="{{ Request::is('ui-grid') ? 'active' : '' }}" href="{{url('ui-grid')}}">Grid</a>
                            </li>
                            <li><a class="{{ Request::is('ui-images') ? 'active' : '' }}"
                                    href="{{url('ui-images')}}">Images</a></li>
                            <li><a class="{{ Request::is('ui-lightbox') ? 'active' : '' }}"
                                    href="{{url('ui-lightbox')}}">Lightbox</a></li>
                            <li><a class="{{ Request::is('ui-media') ? 'active' : '' }}"
                                    href="{{url('ui-media')}}">Media</a></li>
                            <li><a class="{{ Request::is('ui-modals') ? 'active' : '' }}"
                                    href="{{url('ui-modals')}}">Modals</a></li>
                            <li><a class="{{ Request::is('ui-notification') ? 'active' : '' }}"
                                    href="{{url('ui-notification')}}">Notification</a></li>
                            <li><a class="{{ Request::is('ui-offcanvas') ? 'active' : '' }}"
                                    href="{{url('ui-offcanvas')}}">Offcanvas</a></li>
                            <li><a class="{{ Request::is('ui-pagination') ? 'active' : '' }}"
                                    href="{{url('ui-pagination')}}">Pagination</a></li>
                            <li><a class="{{ Request::is('ui-popovers') ? 'active' : '' }}"
                                    href="{{url('ui-popovers')}}">Popovers</a></li>
                            <li><a class="{{ Request::is('ui-progress') ? 'active' : '' }}"
                                    href="{{url('ui-progress')}}">Progress</a></li>
                            <li><a class="{{ Request::is('ui-placeholders') ? 'active' : '' }}"
                                    href="{{url('ui-placeholders')}}">Placeholders</a></li>
                            <li><a class="{{ Request::is('ui-rangeslider') ? 'active' : '' }}"
                                    href="{{url('ui-rangeslider')}}">Range Slider</a></li>
                            <li><a class="{{ Request::is('ui-spinner') ? 'active' : '' }}"
                                    href="{{url('ui-spinner')}}">Spinner</a></li>
                            <li><a class="{{ Request::is('ui-sweetalerts') ? 'active' : '' }}"
                                    href="{{url('ui-sweetalerts')}}">Sweet Alerts</a></li>
                            <li><a class="{{ Request::is('ui-nav-tabs') ? 'active' : '' }}"
                                    href="{{url('ui-nav-tabs')}}">Tabs</a></li>
                            <li><a class="{{ Request::is('ui-toasts') ? 'active' : '' }}"
                                    href="{{url('ui-toasts')}}">Toasts</a></li>
                            <li><a class="{{ Request::is('ui-tooltips') ? 'active' : '' }}"
                                    href="{{url('ui-tooltips')}}">Tooltips</a></li>
                            <li><a class="{{ Request::is('ui-typography') ? 'active' : '' }}"
                                    href="{{url('ui-typography')}}">Typography</a></li>
                            <li><a class="{{ Request::is('ui-video') ? 'active' : '' }}"
                                    href="{{url('ui-video')}}">Video</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('ui-ribbon', 'ui-clipboard', 'ui-drag-drop', 'ui-rangeslider', 'ui-rating', 'ui-text-editor', 'ui-counter', 'ui-scrollbar', 'ui-stickynote', 'ui-timeline') ? 'active' : '' }}"
                        id="v-pills-elements" role="tabpanel" aria-labelledby="v-pills-elements-tab">
                        <p>Advanced UI</p>
                        <ul>
                            <li><a class="{{ Request::is('ui-ribbon') ? 'active' : '' }}"
                                    href="{{url('ui-ribbon')}}">Ribbon</a></li>
                            <li><a class="{{ Request::is('ui-clipboard') ? 'active' : '' }}"
                                    href="{{url('ui-clipboard')}}">Clipboard</a></li>
                            <li><a class="{{ Request::is('ui-drag-drop') ? 'active' : '' }}"
                                    href="{{url('ui-drag-drop')}}">Drag & Drop</a></li>
                            <li><a class="{{ Request::is('ui-rangeslider') ? 'active' : '' }}"
                                    href="{{url('ui-rangeslider')}}">Range Slider</a></li>
                            <li><a class="{{ Request::is('ui-rating') ? 'active' : '' }}"
                                    href="{{url('ui-rating')}}">Rating</a></li>
                            <li><a class="{{ Request::is('ui-text-editor') ? 'active' : '' }}"
                                    href="{{url('ui-text-editor')}}">Text Editor</a></li>
                            <li><a class="{{ Request::is('ui-counter') ? 'active' : '' }}"
                                    href="{{url('ui-counter')}}">Counter</a></li>
                            <li><a class="{{ Request::is('ui-scrollbar') ? 'active' : '' }}"
                                    href="{{url('ui-scrollbar')}}">Scrollbar</a></li>
                            <li><a class="{{ Request::is('ui-stickynote') ? 'active' : '' }}"
                                    href="{{url('ui-stickynote')}}">Sticky Note</a></li>
                            <li><a class="{{ Request::is('ui-timeline') ? 'active' : '' }}"
                                    href="{{url('ui-timeline')}}">Timeline</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('chart-apex', 'chart-js', 'chart-morris', 'chart-flot', 'chart-peity', 'chart-c3') ? 'active' : '' }}"
                        id="v-pills-charts" role="tabpanel" aria-labelledby="v-pills-charts-tab">
                        <p>Charts</p>
                        <ul>
                            <li><a class="{{ Request::is('chart-apex') ? 'active' : '' }}" href="{{url('chart-apex')}}">Apex
                                    Charts</a></li>
                            <li><a class="{{ Request::is('chart-js') ? 'active' : '' }}" href="{{url('chart-js')}}">Chart
                                    Js</a></li>
                            <li><a class="{{ Request::is('chart-morris') ? 'active' : '' }}"
                                    href="{{url('chart-morris')}}">Morris Charts</a></li>
                            <li><a class="{{ Request::is('chart-flot') ? 'active' : '' }}" href="{{url('chart-flot')}}">Flot
                                    Charts</a></li>
                            <li><a class="{{ Request::is('chart-peity') ? 'active' : '' }}"
                                    href="{{url('chart-peity')}}">Peity Charts</a></li>
                            <li><a class="{{ Request::is('chart-c3') ? 'active' : '' }}" href="{{url('chart-c3')}}">C3
                                    Charts</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('icon-fontawesome', 'icon-feather', 'icon-ionic', 'icon-material', 'icon-pe7', 'icon-simpleline', 'icon-themify', 'icon-weather', 'icon-typicon', 'icon-flag') ? 'active' : '' }}"
                        id="v-pills-icons" role="tabpanel" aria-labelledby="v-pills-icons-tab">
                        <p>Icons</p>
                        <ul>
                            <li><a class="{{ Request::is('icon-fontawesome') ? 'active' : '' }}"
                                    href="{{url('icon-fontawesome')}}">Fontawesome Icons</a></li>
                            <li><a class="{{ Request::is('icon-feather') ? 'active' : '' }}"
                                    href="{{url('icon-feather')}}">Feather Icons</a></li>
                            <li><a class="{{ Request::is('icon-ionic') ? 'active' : '' }}"
                                    href="{{url('icon-ionic')}}">Ionic Icons</a></li>
                            <li><a class="{{ Request::is('icon-material') ? 'active' : '' }}"
                                    href="{{url('icon-material')}}">Material Icons</a></li>
                            <li><a class="{{ Request::is('icon-pe7') ? 'active' : '' }}" href="{{url('icon-pe7')}}">Pe7
                                    Icons</a></li>
                            <li><a class="{{ Request::is('icon-simpleline') ? 'active' : '' }}"
                                    href="{{url('icon-simpleline')}}">Simpleline Icons</a></li>
                            <li><a class="{{ Request::is('icon-themify') ? 'active' : '' }}"
                                    href="{{url('icon-themify')}}">Themify Icons</a></li>
                            <li><a class="{{ Request::is('icon-weather') ? 'active' : '' }}"
                                    href="{{url('icon-weather')}}">Weather Icons</a></li>
                            <li><a class="{{ Request::is('icon-typicon') ? 'active' : '' }}"
                                    href="{{url('icon-typicon')}}">Typicon Icons</a></li>
                            <li><a class="{{ Request::is('icon-flag') ? 'active' : '' }}" href="{{url('icon-flag')}}">Flag
                                    Icons</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('form-basic-inputs', 'form-input-groups', 'form-horizontal', 'form-vertical', 'form-mask', 'form-validation', 'form-select2', 'form-fileupload', 'horizontal-timeline', 'form-wizard') ? 'active' : '' }}"
                        id="v-pills-forms" role="tabpanel" aria-labelledby="v-pills-forms-tab">
                        <p>Forms</p>
                        <ul>
                            <li><a class="{{ Request::is('form-basic-inputs') ? 'active' : '' }}"
                                    href="{{url('form-basic-inputs')}}">Basic Inputs </a></li>
                            <li><a class="{{ Request::is('form-input-groups') ? 'active' : '' }}"
                                    href="{{url('form-input-groups')}}">Input Groups </a></li>
                            <li><a class="{{ Request::is('form-horizontal') ? 'active' : '' }}"
                                    href="{{url('form-horizontal')}}">Horizontal Form </a></li>
                            <li><a class="{{ Request::is('form-vertical') ? 'active' : '' }}"
                                    href="{{url('form-vertical')}}"> Vertical Form </a></li>
                            <li><a class="{{ Request::is('form-mask') ? 'active' : '' }}" href="{{url('form-mask')}}"> Form
                                    Mask </a></li>
                            <li><a class="{{ Request::is('form-validation') ? 'active' : '' }}"
                                    href="{{url('form-validation')}}"> Form Validation </a></li>
                            <li><a class="{{ Request::is('form-select2') ? 'active' : '' }}"
                                    href="{{url('form-select2')}}">Form Select2 </a></li>
                            <li><a class="{{ Request::is('form-fileupload') ? 'active' : '' }}"
                                    href="{{url('form-fileupload')}}">File Upload </a></li>
                            <li><a class="{{ Request::is('horizontal-timeline') ? 'active' : '' }}"
                                    href="{{url('horizontal-timeline')}}">Horizontal Timeline</a></li>
                            <li><a class="{{ Request::is('form-wizard') ? 'active' : '' }}"
                                    href="{{url('form-wizard')}}">Form Wizard</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show {{ Request::is('tables-basic', 'data-tables') ? 'active' : '' }}"
                        id="v-pills-tables" role="tabpanel" aria-labelledby="v-pills-tables-tab">
                        <p>Tables</p>
                        <ul>
                            <li><a class="{{ Request::is('tables-basic') ? 'active' : '' }}"
                                    href="{{url('tables-basic')}}">Basic Tables </a></li>
                            <li><a class="{{ Request::is('data-tables') ? 'active' : '' }}"
                                    href="{{url('data-tables')}}">Data Table </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-documentation" role="tabpanel"
                        aria-labelledby="v-pills-documentation-tab">
                        <p>Documentation</p>
                        <ul>
                            <li><a href="#">Documentation </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-changelog" role="tabpanel"
                        aria-labelledby="v-pills-changelog-tab">
                        <p>Change Log</p>
                        <ul>
                            <li><a href="#"><span>Change Log</span> <span class="badge badge-primary ms-auto">v4.0</span>
                                </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-multilevel" role="tabpanel"
                        aria-labelledby="v-pills-multilevel-tab">
                        <p>Multi Level</p>
                        <ul>
                            <li class="sub-menu">
                                <a href="javascript:void(0);">Level 1 <span class="menu-arrow"></span></a>
                                <ul class="ms-3">
                                    <li class="sub-menu">
                                        <a href="javascript:void(0);">Level 1 <span class="menu-arrow"></span></a>
                                        <ul>
                                            <li><a href="javascript:void(0);">Level 2</a></li>
                                            <li><a href="javascript:void(0);">Level 3</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="javascript:void(0);">Level 2</a></li>
                            <li><a href="javascript:void(0);">Level 3</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Two Col Sidebar -->
@endif

@if (Route::is(['chat', 'voice-call', 'video-call', 'outgoing-call', 'incoming-call', 'events', 'contacts', 'inbox', 'file-manager']))

    <!-- Two Col Sidebar -->
    <div class="two-col-bar" id="two-col-bar">
        <div class="sidebar sidebar-twocol">
            <div class="sidebar-left slimscroll">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link" id="v-pills-dashboard-tab" title="Dashboard" data-bs-toggle="pill"
                        href="#v-pills-dashboard" role="tab" aria-controls="v-pills-dashboard" aria-selected="true">
                        <span class="material-icons-outlined">
                            home
                        </span>
                    </a>
                    <a class="nav-link active" id="v-pills-apps-tab" title="Apps" data-bs-toggle="pill" href="#v-pills-apps"
                        role="tab" aria-controls="v-pills-apps" aria-selected="false">
                        <span class="material-icons-outlined">
                            dashboard
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-employees-tab" title="Employees" data-bs-toggle="pill"
                        href="#v-pills-employees" role="tab" aria-controls="v-pills-employees" aria-selected="false">
                        <span class="material-icons-outlined">
                            people
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-clients-tab" title="Clients" data-bs-toggle="pill"
                        href="#v-pills-clients" role="tab" aria-controls="v-pills-clients" aria-selected="false">
                        <span class="material-icons-outlined">
                            person
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-projects-tab" title="Projects" data-bs-toggle="pill"
                        href="#v-pills-projects" role="tab" aria-controls="v-pills-projects" aria-selected="false">
                        <span class="material-icons-outlined">
                            topic
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-leads-tab" title="CRM" data-bs-toggle="pill" href="#v-pills-leads"
                        role="tab" aria-controls="v-pills-leads" aria-selected="false">
                        <span class="material-icons-outlined">
                            leaderboard
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-tickets-tab" title="Tickets" data-bs-toggle="pill"
                        href="#v-pills-tickets" role="tab" aria-controls="v-pills-tickets" aria-selected="false">
                        <span class="material-icons-outlined">
                            confirmation_number
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-sales-tab" title="Sales" data-bs-toggle="pill" href="#v-pills-sales"
                        role="tab" aria-controls="v-pills-sales" aria-selected="false">
                        <span class="material-icons-outlined">
                            shopping_bag
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-accounting-tab" title="Accounting" data-bs-toggle="pill"
                        href="#v-pills-accounting" role="tab" aria-controls="v-pills-accounting" aria-selected="false">
                        <span class="material-icons-outlined">
                            account_balance_wallet
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-payroll-tab" title="Payroll" data-bs-toggle="pill"
                        href="#v-pills-payroll" role="tab" aria-controls="v-pills-payroll" aria-selected="false">
                        <span class="material-icons-outlined">
                            request_quote
                        </span>
                    </a>

                    <a class="nav-link" id="v-pills-policies-tab" title="Policies" data-bs-toggle="pill"
                        href="#v-pills-policies" role="tab" aria-controls="v-pills-policies" aria-selected="false">
                        <span class="material-icons-outlined">
                            verified_user
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-reports-tab" title="Reports" data-bs-toggle="pill"
                        href="#v-pills-reports" role="tab" aria-controls="v-pills-reports" aria-selected="false">
                        <span class="material-icons-outlined">
                            report_gmailerrorred
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-performance-tab" title="Performance" data-bs-toggle="pill"
                        href="#v-pills-performance" role="tab" aria-controls="v-pills-performance" aria-selected="false">
                        <span class="material-icons-outlined">
                            shutter_speed
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-goals-tab" title="Goals" data-bs-toggle="pill" href="#v-pills-goals"
                        role="tab" aria-controls="v-pills-goals" aria-selected="false">
                        <span class="material-icons-outlined">
                            track_changes
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-training-tab" title="Training" data-bs-toggle="pill"
                        href="#v-pills-training" role="tab" aria-controls="v-pills-training" aria-selected="false">
                        <span class="material-icons-outlined">
                            checklist_rtl
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-promotion-tab" title="Promotions" data-bs-toggle="pill"
                        href="#v-pills-promotion" role="tab" aria-controls="v-pills-promotion" aria-selected="false">
                        <span class="material-icons-outlined">
                            auto_graph
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-resignation-tab" title="Resignation" data-bs-toggle="pill"
                        href="#v-pills-resignation" role="tab" aria-controls="v-pills-resignation" aria-selected="false">
                        <span class="material-icons-outlined">
                            do_not_disturb_alt
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-termination-tab" title="Termination" data-bs-toggle="pill"
                        href="#v-pills-termination" role="tab" aria-controls="v-pills-termination" aria-selected="false">
                        <span class="material-icons-outlined">
                            indeterminate_check_box
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-assets-tab" title="Assets" data-bs-toggle="pill" href="#v-pills-assets"
                        role="tab" aria-controls="v-pills-assets" aria-selected="false">
                        <span class="material-icons-outlined">
                            web_asset
                        </span>
                    </a>
                    <a class="nav-link " id="v-pills-jobs-tab" title="Jobs" data-bs-toggle="pill" href="#v-pills-jobs"
                        role="tab" aria-controls="v-pills-jobs" aria-selected="false">
                        <span class="material-icons-outlined">
                            work_outline
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-knowledgebase-tab" title="Knowledgebase" data-bs-toggle="pill"
                        href="#v-pills-knowledgebase" role="tab" aria-controls="v-pills-knowledgebase"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            school
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-activities-tab" title="Activities" data-bs-toggle="pill"
                        href="#v-pills-activities" role="tab" aria-controls="v-pills-activities" aria-selected="false">
                        <span class="material-icons-outlined">
                            toggle_off
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-users-tab" title="Users" data-bs-toggle="pill" href="#v-pills-users"
                        role="tab" aria-controls="v-pills-users" aria-selected="false">
                        <span class="material-icons-outlined">
                            group_add
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-settings-tab" title="Settings" data-bs-toggle="pill"
                        href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                        <span class="material-icons-outlined">
                            settings
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-profile-tab" title="Profile" data-bs-toggle="pill"
                        href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                        <span class="material-icons-outlined">
                            manage_accounts
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-authentication-tab" title="Authentication" data-bs-toggle="pill"
                        href="#v-pills-authentication" role="tab" aria-controls="v-pills-authentication"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            perm_contact_calendar
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-errorpages-tab" title="Error Pages" data-bs-toggle="pill"
                        href="#v-pills-errorpages" role="tab" aria-controls="v-pills-errorpages" aria-selected="false">
                        <span class="material-icons-outlined">
                            announcement
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-subscriptions-tab" title="Subscriptions" data-bs-toggle="pill"
                        href="#v-pills-subscriptions" role="tab" aria-controls="v-pills-subscriptions"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            loyalty
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-pages-tab" title="Pages" data-bs-toggle="pill" href="#v-pills-pages"
                        role="tab" aria-controls="v-pills-pages" aria-selected="false">
                        <span class="material-icons-outlined">
                            layers
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-baseui-tab" title="Base UI" data-bs-toggle="pill" href="#v-pills-baseui"
                        role="tab" aria-controls="v-pills-baseui" aria-selected="false">
                        <span class="material-icons-outlined">
                            foundation
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-elements-tab" title="Advanced UI" data-bs-toggle="pill"
                        href="#v-pills-elements" role="tab" aria-controls="v-pills-elements" aria-selected="false">
                        <span class="material-icons-outlined">
                            bento
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-charts-tab" title="Charts" data-bs-toggle="pill" href="#v-pills-charts"
                        role="tab" aria-controls="v-pills-charts" aria-selected="false">
                        <span class="material-icons-outlined">
                            bar_chart
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-icons-tab" title="Icons" data-bs-toggle="pill" href="#v-pills-icons"
                        role="tab" aria-controls="v-pills-icons" aria-selected="false">
                        <span class="material-icons-outlined">
                            grading
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-forms-tab" title="Forms" data-bs-toggle="pill" href="#v-pills-forms"
                        role="tab" aria-controls="v-pills-forms" aria-selected="false">
                        <span class="material-icons-outlined">
                            view_day
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-tables-tab" title="Tables" data-bs-toggle="pill" href="#v-pills-tables"
                        role="tab" aria-controls="v-pills-tables" aria-selected="false">
                        <span class="material-icons-outlined">
                            table_rows
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-documentation-tab" title="Documentation" data-bs-toggle="pill"
                        href="#v-pills-documentation" role="tab" aria-controls="v-pills-documentation"
                        aria-selected="false">
                        <span class="material-icons-outlined">
                            description
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-changelog-tab" title="Changelog" data-bs-toggle="pill"
                        href="#v-pills-changelog" role="tab" aria-controls="v-pills-changelog" aria-selected="false">
                        <span class="material-icons-outlined">
                            sync_alt
                        </span>
                    </a>
                    <a class="nav-link" id="v-pills-multilevel-tab" title="Multilevel" data-bs-toggle="pill"
                        href="#v-pills-multilevel" role="tab" aria-controls="v-pills-multilevel" aria-selected="false">
                        <span class="material-icons-outlined">
                            library_add_check
                        </span>
                    </a>
                </div>
            </div>

            <div class="sidebar-right">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade" id="v-pills-dashboard" role="tabpanel"
                        aria-labelledby="v-pills-dashboard-tab">
                        <p>Dashboard</p>
                        <ul>
                            <li><a class="{{ Request::is('admin-dashboard') ? 'active' : '' }}"
                                    href="{{url('admin-dashboard')}}">Admin Dashboard</a></li>
                            <li><a class="{{ Request::is('employee-dashboard') ? 'active' : '' }}"
                                    href="{{url('employee-dashboard')}}">Employee Dashboard</a></li>
                            <li><a class="{{ Request::is('deals-dashboard') ? 'active' : '' }}"
                                    href="{{url('deals-dashboard')}}">Deals Dashboard</a></li>
                            <li><a class="{{ Request::is('leads-dashboard') ? 'active' : '' }}"
                                    href="{{url('leads-dashboard')}}">Leads Dashboard</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade show active" id="v-pills-apps" role="tabpanel"
                        aria-labelledby="v-pills-apps-tab">
                        <p>App</p>
                        <ul>
                            <li>
                                <a class="{{ Request::is('chat') ? 'active' : '' }}" href="{{url('chat')}}"
                                    class="active">Chat</a>
                            </li>
                            <li class="sub-menu">
                                <a href="#">Calls <span class="menu-arrow"></span></a>
                                <ul>
                                    <li><a class="{{ Request::is('voice-call') ? 'active' : '' }}"
                                            href="{{url('voice-call')}}">Voice Call</a></li>
                                    <li><a class="{{ Request::is('video-call') ? 'active' : '' }}"
                                            href="{{url('video-call')}}">Video Call</a></li>
                                    <li><a class="{{ Request::is('outgoing-call') ? 'active' : '' }}"
                                            href="{{url('outgoing-call')}}">Outgoing Call</a></li>
                                    <li><a class="{{ Request::is('incoming-call') ? 'active' : '' }}"
                                            href="{{url('incoming-call')}}">Incoming Call</a></li>
                                </ul>
                            </li>
                            <li>
                                <a class="{{ Request::is('events') ? 'active' : '' }}" href="{{url('events')}}">Calendar</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('contacts') ? 'active' : '' }}"
                                    href="{{url('contacts')}}">Contacts</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('inbox') ? 'active' : '' }}" href="{{url('inbox')}}">Email</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('file-manager') ? 'active' : '' }}"
                                    href="{{url('file-manager')}}">File Manager</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-employees" role="tabpanel"
                        aria-labelledby="v-pills-employees-tab">
                        <p>Employees</p>
                        <ul>
                            <li><a class="{{ Request::is('employees', 'employees-list') ? 'active' : '' }}"
                                    href="{{url('employees')}}">All Employees</a></li>
                            <li><a class="{{ Request::is('holidays') ? 'active' : '' }}"
                                    href="{{url('holidays')}}">Holidays</a></li>
                            <li><a class="{{ Request::is('leaves') ? 'active' : '' }}" href="{{url('leaves')}}">Leaves
                                    (Admin) <span class="badge rounded-pill bg-primary float-end">1</span></a></li>
                            <li><a class="{{ Request::is('leaves-employee') ? 'active' : '' }}"
                                    href="{{url('leaves-employee')}}">Leaves (Employee)</a></li>
                            <li><a class="{{ Request::is('leave-settings') ? 'active' : '' }}"
                                    href="{{url('leave-settings')}}">Leave Settings</a></li>
                            <li><a class="{{ Request::is('attendance') ? 'active' : '' }}"
                                    href="{{url('attendance')}}">Attendance (Admin)</a></li>
                            <li><a class="{{ Request::is('attendance-employee') ? 'active' : '' }}"
                                    href="{{url('attendance-employee')}}">Attendance (Employee)</a></li>
                            <li><a class="{{ Request::is('departments') ? 'active' : '' }}"
                                    href="{{url('departments')}}">Departments</a></li>
                            <li><a class="{{ Request::is('designations') ? 'active' : '' }}"
                                    href="{{url('designations')}}">Designations</a></li>
                            <li><a class="{{ Request::is('timesheet') ? 'active' : '' }}"
                                    href="{{url('timesheet')}}">Timesheet</a></li>
                            <li><a class="{{ Request::is('shift-scheduling', 'shift-list') ? 'active' : '' }}"
                                    href="{{url('shift-scheduling')}}">Shift & Schedule</a></li>
                            <li><a class="{{ Request::is('overtime') ? 'active' : '' }}"
                                    href="{{url('overtime')}}">Overtime</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-clients" role="tabpanel" aria-labelledby="v-pills-clients-tab">
                        <p>Clients</p>
                        <ul>
                            <li><a class="{{ Request::is('clients', 'clients-list') ? 'active' : '' }}"
                                    href="{{url('clients')}}">Clients</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-projects" role="tabpanel" aria-labelledby="v-pills-projects-tab">
                        <p>Projects</p>
                        <ul>
                            <li><a class="{{ Request::is('projects', 'project-list') ? 'active' : '' }}"
                                    href="{{url('projects')}}">Projects</a></li>
                            <li><a class="{{ Request::is('tasks') ? 'active' : '' }}" href="{{url('tasks')}}">Tasks</a></li>
                            <li><a class="{{ Request::is('task-board') ? 'active' : '' }}" href="{{url('task-board')}}">Task
                                    Board</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-leads" role="tabpanel" aria-labelledby="v-pills-leads-tab">
                        <p>CRM</p>
                        <ul>
                            <li>
                                <a class="{{ Request::is('contact-list', 'contact-grid', 'contact-details') ? 'active' : '' }}"
                                    href="{{url('contact-list')}}"> Contacts</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('companies', 'companies-grid', 'company-details') ? 'active' : '' }}"
                                    href="{{url('companies')}}">Companies</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('deals', 'deals-kanban', 'deals-details') ? 'active' : '' }}"
                                    href="{{url('deals')}}"> Deals</a>
                            </li>
                            <li>
                                <a class="{{ Request::is('leads', 'leads-details', 'leads-kanban') ? 'active' : '' }}"
                                    href="{{url('leads')}}"> Leads </a>
                            </li>
                            <li>
                                <a class="{{ Request::is('pipeline') ? 'active' : '' }}" href="{{url('pipeline')}}">Pipeline
                                </a>
                            </li>
                            <li>
                                <a class="{{ Request::is('analytics') ? 'active' : '' }}"
                                    href="{{url('analytics')}}">Analytics</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-tickets" role="tabpanel" aria-labelledby="v-pills-tickets-tab">
                        <p>Tickets</p>
                        <ul>
                            <li><a class="{{ Request::is('tickets') ? 'active' : '' }}"
                                    href="{{url('tickets')}}">Tickets</a></li>
                            <li><a class="{{ Request::is('ticket-details') ? 'active' : '' }}"
                                    href="{{url('ticket-details')}}">Ticket Details</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-sales" role="tabpanel" aria-labelledby="v-pills-sales-tab">
                        <p>Sales</p>
                        <ul>
                            <li><a class="{{ Request::is('estimates', 'edit-estimate', 'create-estimate') ? 'active' : '' }}"
                                    href="{{url('estimates')}}">Estimates</a></li>
                            <li><a class="{{ Request::is('invoices', 'create-invoice', 'edit-invoice') ? 'active' : '' }}"
                                    href="{{url('invoices')}}">Invoices</a></li>
                            <li><a class="{{ Request::is('payments') ? 'active' : '' }}"
                                    href="{{url('payments')}}">Payments</a></li>
                            <li><a class="{{ Request::is('expenses') ? 'active' : '' }}"
                                    href="{{url('expenses')}}">Expenses</a></li>
                            <li><a class="{{ Request::is('provident-fund') ? 'active' : '' }}"
                                    href="{{url('provident-fund')}}">Provident Fund</a></li>
                            <li><a class="{{ Request::is('taxes') ? 'active' : '' }}" href="{{url('taxes')}}">Taxes</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-accounting" role="tabpanel"
                        aria-labelledby="v-pills-accounting-tab">
                        <p>Accounting</p>
                        <ul>
                            <li><a class="{{ Request::is('categories', 'sub-category') ? 'active' : '' }}"
                                    href="{{url('categories')}}">Categories</a></li>
                            <li><a class="{{ Request::is('budgets') ? 'active' : '' }}"
                                    href="{{url('budgets')}}">Budgets</a></li>
                            <li><a class="{{ Request::is('budget-expenses') ? 'active' : '' }}"
                                    href="{{url('budget-expenses')}}">Budget Expenses</a></li>
                            <li><a class="{{ Request::is('budget-revenues') ? 'active' : '' }}"
                                    href="{{url('budget-revenues')}}">Budget Revenues</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-payroll" role="tabpanel" aria-labelledby="v-pills-payroll-tab">
                        <p>Payroll</p>
                        <ul>
                            <li><a class="{{ Request::is('salary') ? 'active' : '' }}" href="{{url('salary')}}"> Employee
                                    Salary </a></li>
                            <li><a class="{{ Request::is('salary-view') ? 'active' : '' }}" href="{{url('salary-view')}}">
                                    Payslip </a></li>
                            <li><a class="{{ Request::is('payroll-items') ? 'active' : '' }}"
                                    href="{{url('payroll-items')}}"> Payroll Items </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-policies" role="tabpanel" aria-labelledby="v-pills-policies-tab">
                        <p>Policies</p>
                        <ul>
                            <li><a class="{{ Request::is('policies') ? 'active' : '' }}" href="{{url('policies')}}">
                                    Policies </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-reports" role="tabpanel" aria-labelledby="v-pills-reports-tab">
                        <p>Reports</p>
                        <ul>
                            <li><a class="{{ Request::is('expense-reports') ? 'active' : '' }}"
                                    href="{{url('expense-reports')}}"> Expense Report </a></li>
                            <li><a class="{{ Request::is('invoice-reports') ? 'active' : '' }}"
                                    href="{{url('invoice-reports')}}"> Invoice Report </a></li>
                            <li><a class="{{ Request::is('payments-reports') ? 'active' : '' }}"
                                    href="{{url('payments-reports')}}"> Payments Report </a></li>
                            <li><a class="{{ Request::is('project-reports') ? 'active' : '' }}"
                                    href="{{url('project-reports')}}"> Project Report </a></li>
                            <li><a class="{{ Request::is('task-reports') ? 'active' : '' }}" href="{{url('task-reports')}}">
                                    Task Report </a></li>
                            <li><a class="{{ Request::is('user-reports') ? 'active' : '' }}" href="{{url('user-reports')}}">
                                    User Report </a></li>
                            <li><a class="{{ Request::is('employee-reports') ? 'active' : '' }}"
                                    href="{{url('employee-reports')}}"> Employee Report </a></li>
                            <li><a class="{{ Request::is('payslip-reports') ? 'active' : '' }}"
                                    href="{{url('payslip-reports')}}"> Payslip Report </a></li>
                            <li><a class="{{ Request::is('attendance-reports') ? 'active' : '' }}"
                                    href="{{url('attendance-reports')}}"> Attendance Report </a></li>
                            <li><a class="{{ Request::is('leave-reports') ? 'active' : '' }}"
                                    href="{{url('leave-reports')}}"> Leave Report </a></li>
                            <li><a class="{{ Request::is('daily-reports') ? 'active' : '' }}"
                                    href="{{url('daily-reports')}}"> Daily Report </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-performance" role="tabpanel"
                        aria-labelledby="v-pills-performance-tab">
                        <p>Performance</p>
                        <ul>
                            <li><a class="{{ Request::is('performance-indicator') ? 'active' : '' }}"
                                    href="{{url('performance-indicator')}}"> Performance Indicator </a></li>
                            <li><a class="{{ Request::is('performance') ? 'active' : '' }}" href="{{url('performance')}}">
                                    Performance Review </a></li>
                            <li><a class="{{ Request::is('performance-appraisal') ? 'active' : '' }}"
                                    href="{{url('performance-appraisal')}}"> Performance Appraisal </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-goals" role="tabpanel" aria-labelledby="v-pills-goals-tab">
                        <p>Goals</p>
                        <ul>
                            <li><a class="{{ Request::is('goal-tracking') ? 'active' : '' }}"
                                    href="{{url('goal-tracking')}}"> Goal List </a></li>
                            <li><a class="{{ Request::is('goal-type') ? 'active' : '' }}" href="{{url('goal-type')}}"> Goal
                                    Type </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-training" role="tabpanel" aria-labelledby="v-pills-training-tab">
                        <p>Training</p>
                        <ul>
                            <li><a class="{{ Request::is('training') ? 'active' : '' }}" href="{{url('training')}}">
                                    Training List </a></li>
                            <li><a class="{{ Request::is('trainers') ? 'active' : '' }}" href="{{url('trainers')}}">
                                    Trainers</a></li>
                            <li><a class="{{ Request::is('training-type') ? 'active' : '' }}"
                                    href="{{url('training-type')}}"> Training Type </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-promotion" role="tabpanel"
                        aria-labelledby="v-pills-promotion-tab">
                        <p>Promotion</p>
                        <ul>
                            <li><a class="{{ Request::is('promotion') ? 'active' : '' }}" href="{{url('promotion')}}">
                                    Promotion </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-resignation" role="tabpanel"
                        aria-labelledby="v-pills-resignation-tab">
                        <p>Resignation</p>
                        <ul>
                            <li><a class="{{ Request::is('resignation') ? 'active' : '' }}" href="{{url('resignation')}}">
                                    Resignation </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-termination" role="tabpanel"
                        aria-labelledby="v-pills-termination-tab">
                        <p>Termination</p>
                        <ul>
                            <li><a class="{{ Request::is('termination') ? 'active' : '' }}" href="{{url('termination')}}">
                                    Termination </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-assets" role="tabpanel" aria-labelledby="v-pills-assets-tab">
                        <p>Assets</p>
                        <ul>
                            <li><a class="{{ Request::is('assets1') ? 'active' : '' }}" href="{{url('assets1')}}"> Assets
                                </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade " id="v-pills-jobs" role="tabpanel" aria-labelledby="v-pills-jobs-tab">
                        <p>Jobs</p>
                        <ul>
                            <li><a class="{{ Request::is('user-dashboard', 'user-all-jobs', 'saved-jobs', 'applied-jobs', 'interviewing', 'offered-jobs', 'visited-jobs', 'archived-jobs', 'job-aptitude', 'questions') ? 'active' : '' }}"
                                    href="{{url('user-dashboard')}}" class="active"> User Dasboard </a></li>
                            <li><a class="{{ Request::is('jobs-dashboard') ? 'active' : '' }}"
                                    href="{{url('jobs-dashboard')}}"> Jobs Dasboard </a></li>
                            <li><a class="{{ Request::is('jobs') ? 'active' : '' }}" href="{{url('jobs')}}"> Manage Jobs
                                </a></li>
                            <li><a class="{{ Request::is('job-applicants') ? 'active' : '' }}"
                                    href="{{url('job-applicants')}}"> Applied Jobs </a></li>
                            <li><a class="{{ Request::is('manage-resumes') ? 'active' : '' }}"
                                    href="{{url('manage-resumes')}}"> Manage Resumes </a></li>
                            <li><a class="{{ Request::is('shortlist-candidates') ? 'active' : '' }}"
                                    href="{{url('shortlist-candidates')}}"> Shortlist Candidates </a></li>
                            <li><a class="{{ Request::is('interview-questions') ? 'active' : '' }}"
                                    href="{{url('interview-questions')}}"> Interview Questions </a></li>
                            <li><a class="{{ Request::is('offer_approvals') ? 'active' : '' }}"
                                    href="{{url('offer_approvals')}}"> Offer Approvals </a></li>
                            <li><a class="{{ Request::is('experiance-level') ? 'active' : '' }}"
                                    href="{{url('experiance-level')}}"> Experience Level </a></li>
                            <li><a class="{{ Request::is('candidates') ? 'active' : '' }}" href="{{url('candidates')}}">
                                    Candidates List </a></li>
                            <li><a class="{{ Request::is('schedule-timing') ? 'active' : '' }}"
                                    href="{{url('schedule-timing')}}"> Schedule timing </a></li>
                            <li><a class="{{ Request::is('apptitude-result') ? 'active' : '' }}"
                                    href="{{url('apptitude-result')}}"> Aptitude Results </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-knowledgebase" role="tabpanel"
                        aria-labelledby="v-pills-knowledgebase-tab">
                        <p>Knowledgebase</p>
                        <ul>
                            <li><a class="{{ Request::is('knowledgebase', 'knowledgebase-view') ? 'active' : '' }}"
                                    href="{{url('knowledgebase')}}"> Knowledgebase </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-activities" role="tabpanel"
                        aria-labelledby="v-pills-activities-tab">
                        <p>Activities</p>
                        <ul>
                            <li><a class="{{ Request::is('activities') ? 'active' : '' }}" href="{{url('activities')}}">
                                    Activities </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-users" role="tabpanel" aria-labelledby="v-pills-activities-tab">
                        <p>Users</p>
                        <ul>
                            <li><a class="{{ Request::is('users') ? 'active' : '' }}" href="{{url('users')}}"> Users </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                        <p>Settings</p>
                        <ul>
                            <li><a class="{{ Request::is('settings', 'localization', 'theme-settings', 'roles-permissions', 'email-settings', 'performance-setting', 'approval-setting', 'invoice-settings', 'salary-settings', 'notifications-settings', 'change-password', 'leave-type', 'toxbox-setting', 'cron-setting') ? 'active' : '' }}"
                                    href="{{url('settings')}}"> Settings </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        <p>Profile</p>
                        <ul>
                            <li><a class="{{ Request::is('profile', 'user-asset-details') ? 'active' : '' }}"
                                    href="{{url('profile')}}"> Employee Profile </a></li>
                            <li><a class="{{ Request::is('client-profile') ? 'active' : '' }}"
                                    href="{{url('client-profile')}}"> Client Profile </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-authentication" role="tabpanel"
                        aria-labelledby="v-pills-authentication-tab">
                        <p>Authentication</p>
                        <ul>
                            <li><a class="{{ Request::is('index') ? 'active' : '' }}" href="{{url('index')}}"> Login </a>
                            </li>
                            <li><a class="{{ Request::is('register') ? 'active' : '' }}" href="{{url('register')}}">
                                    Register </a></li>
                            <li><a class="{{ Request::is('forgot-password') ? 'active' : '' }}"
                                    href="{{url('forgot-password')}}"> Forgot Password </a></li>
                            <li><a class="{{ Request::is('otp') ? 'active' : '' }}" href="{{url('otp')}}"> OTP </a></li>
                            <li><a class="{{ Request::is('lock-screen') ? 'active' : '' }}" href="{{url('lock-screen')}}">
                                    Lock Screen </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-errorpages" role="tabpanel"
                        aria-labelledby="v-pills-errorpages-tab">
                        <p>Error Pages</p>
                        <ul>
                            <li><a class="{{ Request::is('error-404') ? 'active' : '' }}" href="{{url('error-404')}}">404
                                    Error </a></li>
                            <li><a class="{{ Request::is('error-500') ? 'active' : '' }}" href="{{url('error-500')}}">500
                                    Error </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-subscriptions" role="tabpanel"
                        aria-labelledby="v-pills-subscriptions-tab">
                        <p>Subscriptions</p>
                        <ul>
                            <li><a class="{{ Request::is('subscriptions') ? 'active' : '' }}"
                                    href="{{url('subscriptions')}}"> Subscriptions (Admin) </a></li>
                            <li><a class="{{ Request::is('subscriptions-company') ? 'active' : '' }}"
                                    href="{{url('subscriptions-company')}}"> Subscriptions (Company) </a></li>
                            <li><a class="{{ Request::is('subscribed-companies') ? 'active' : '' }}"
                                    href="{{url('subscribed-companies')}}"> Subscribed Companies</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-pages" role="tabpanel" aria-labelledby="v-pills-pages-tab">
                        <p>Pages</p>
                        <ul>
                            <li><a class="{{ Request::is('search') ? 'active' : '' }}" href="{{url('search')}}"> Search </a>
                            </li>
                            <li><a class="{{ Request::is('faq') ? 'active' : '' }}" href="{{url('faq')}}"> FAQ </a></li>
                            <li><a class="{{ Request::is('terms') ? 'active' : '' }}" href="{{url('terms')}}"> Terms </a>
                            </li>
                            <li><a class="{{ Request::is('privacy-policy') ? 'active' : '' }}"
                                    href="{{url('privacy-policy')}}"> Privacy Policy </a></li>
                            <li><a class="{{ Request::is('blank-page') ? 'active' : '' }}" href="{{url('blank-page')}}">
                                    Blank Page </a></li>
                            <li><a class="{{ Request::is('coming-soon') ? 'active' : '' }}"
                                    href="{{url('coming-soon')}}">Coming Soon </a></li>
                            <li><a class="{{ Request::is('under-maintenance') ? 'active' : '' }}"
                                    href="{{url('under-maintenance')}}">Under Maintanance </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-baseui" role="tabpanel" aria-labelledby="v-pills-baseui-tab">
                        <p>Base UI</p>
                        <ul>
                            <li><a class="{{ Request::is('ui-alerts') ? 'active' : '' }}"
                                    href="{{url('ui-alerts')}}">Alerts</a></li>
                            <li><a class="{{ Request::is('ui-accordion') ? 'active' : '' }}"
                                    href="{{url('ui-accordion')}}">Accordion</a></li>
                            <li><a class="{{ Request::is('ui-avatar') ? 'active' : '' }}"
                                    href="{{url('ui-avatar')}}">Avatar</a></li>
                            <li><a class="{{ Request::is('ui-badges') ? 'active' : '' }}"
                                    href="{{url('ui-badges')}}">Badges</a></li>
                            <li><a class="{{ Request::is('ui-borders') ? 'active' : '' }}"
                                    href="{{url('ui-borders')}}">Border</a></li>
                            <li><a class="{{ Request::is('ui-buttons') ? 'active' : '' }}"
                                    href="{{url('ui-buttons')}}">Buttons</a></li>
                            <li><a class="{{ Request::is('ui-buttons-group') ? 'active' : '' }}"
                                    href="{{url('ui-buttons-group')}}">Button Group</a></li>
                            <li><a class="{{ Request::is('ui-breadcrumb') ? 'active' : '' }}"
                                    href="{{url('ui-breadcrumb')}}">Breadcrumb</a></li>
                            <li><a class="{{ Request::is('ui-cards') ? 'active' : '' }}" href="{{url('ui-cards')}}">Card</a>
                            </li>
                            <li><a class="{{ Request::is('ui-carousel') ? 'active' : '' }}"
                                    href="{{url('ui-carousel')}}">Carousel</a></li>
                            <li><a class="{{ Request::is('ui-colors') ? 'active' : '' }}"
                                    href="{{url('ui-colors')}}">Colors</a></li>
                            <li><a class="{{ Request::is('ui-dropdowns') ? 'active' : '' }}"
                                    href="{{url('ui-dropdowns')}}">Dropdowns</a></li>
                            <li><a class="{{ Request::is('ui-grid') ? 'active' : '' }}" href="{{url('ui-grid')}}">Grid</a>
                            </li>
                            <li><a class="{{ Request::is('ui-images') ? 'active' : '' }}"
                                    href="{{url('ui-images')}}">Images</a></li>
                            <li><a class="{{ Request::is('ui-lightbox') ? 'active' : '' }}"
                                    href="{{url('ui-lightbox')}}">Lightbox</a></li>
                            <li><a class="{{ Request::is('ui-media') ? 'active' : '' }}"
                                    href="{{url('ui-media')}}">Media</a></li>
                            <li><a class="{{ Request::is('ui-modals') ? 'active' : '' }}"
                                    href="{{url('ui-modals')}}">Modals</a></li>
                            <li><a class="{{ Request::is('ui-notification') ? 'active' : '' }}"
                                    href="{{url('ui-notification')}}">Notification</a></li>
                            <li><a class="{{ Request::is('ui-offcanvas') ? 'active' : '' }}"
                                    href="{{url('ui-offcanvas')}}">Offcanvas</a></li>
                            <li><a class="{{ Request::is('ui-pagination') ? 'active' : '' }}"
                                    href="{{url('ui-pagination')}}">Pagination</a></li>
                            <li><a class="{{ Request::is('ui-popovers') ? 'active' : '' }}"
                                    href="{{url('ui-popovers')}}">Popovers</a></li>
                            <li><a class="{{ Request::is('ui-progress') ? 'active' : '' }}"
                                    href="{{url('ui-progress')}}">Progress</a></li>
                            <li><a class="{{ Request::is('ui-placeholders') ? 'active' : '' }}"
                                    href="{{url('ui-placeholders')}}">Placeholders</a></li>
                            <li><a class="{{ Request::is('ui-rangeslider') ? 'active' : '' }}"
                                    href="{{url('ui-rangeslider')}}">Range Slider</a></li>
                            <li><a class="{{ Request::is('ui-spinner') ? 'active' : '' }}"
                                    href="{{url('ui-spinner')}}">Spinner</a></li>
                            <li><a class="{{ Request::is('ui-sweetalerts') ? 'active' : '' }}"
                                    href="{{url('ui-sweetalerts')}}">Sweet Alerts</a></li>
                            <li><a class="{{ Request::is('ui-nav-tabs') ? 'active' : '' }}"
                                    href="{{url('ui-nav-tabs')}}">Tabs</a></li>
                            <li><a class="{{ Request::is('ui-toasts') ? 'active' : '' }}"
                                    href="{{url('ui-toasts')}}">Toasts</a></li>
                            <li><a class="{{ Request::is('ui-tooltips') ? 'active' : '' }}"
                                    href="{{url('ui-tooltips')}}">Tooltips</a></li>
                            <li><a class="{{ Request::is('ui-typography') ? 'active' : '' }}"
                                    href="{{url('ui-typography')}}">Typography</a></li>
                            <li><a class="{{ Request::is('ui-video') ? 'active' : '' }}"
                                    href="{{url('ui-video')}}">Video</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-elements" role="tabpanel" aria-labelledby="v-pills-elements-tab">
                        <p>Advanced UI</p>
                        <ul>
                            <li><a class="{{ Request::is('ui-ribbon') ? 'active' : '' }}"
                                    href="{{url('ui-ribbon')}}">Ribbon</a></li>
                            <li><a class="{{ Request::is('ui-clipboard') ? 'active' : '' }}"
                                    href="{{url('ui-clipboard')}}">Clipboard</a></li>
                            <li><a class="{{ Request::is('ui-drag-drop') ? 'active' : '' }}"
                                    href="{{url('ui-drag-drop')}}">Drag & Drop</a></li>
                            <li><a class="{{ Request::is('ui-rangeslider') ? 'active' : '' }}"
                                    href="{{url('ui-rangeslider')}}">Range Slider</a></li>
                            <li><a class="{{ Request::is('ui-rating') ? 'active' : '' }}"
                                    href="{{url('ui-rating')}}">Rating</a></li>
                            <li><a class="{{ Request::is('ui-text-editor') ? 'active' : '' }}"
                                    href="{{url('ui-text-editor')}}">Text Editor</a></li>
                            <li><a class="{{ Request::is('ui-counter') ? 'active' : '' }}"
                                    href="{{url('ui-counter')}}">Counter</a></li>
                            <li><a class="{{ Request::is('ui-scrollbar') ? 'active' : '' }}"
                                    href="{{url('ui-scrollbar')}}">Scrollbar</a></li>
                            <li><a class="{{ Request::is('ui-stickynote') ? 'active' : '' }}"
                                    href="{{url('ui-stickynote')}}">Sticky Note</a></li>
                            <li><a class="{{ Request::is('ui-timeline') ? 'active' : '' }}"
                                    href="{{url('ui-timeline')}}">Timeline</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-charts" role="tabpanel" aria-labelledby="v-pills-charts-tab">
                        <p>Charts</p>
                        <ul>
                            <li><a class="{{ Request::is('chart-apex') ? 'active' : '' }}" href="{{url('chart-apex')}}">Apex
                                    Charts</a></li>
                            <li><a class="{{ Request::is('chart-js') ? 'active' : '' }}" href="{{url('chart-js')}}">Chart
                                    Js</a></li>
                            <li><a class="{{ Request::is('chart-morris') ? 'active' : '' }}"
                                    href="{{url('chart-morris')}}">Morris Charts</a></li>
                            <li><a class="{{ Request::is('chart-flot') ? 'active' : '' }}" href="{{url('chart-flot')}}">Flot
                                    Charts</a></li>
                            <li><a class="{{ Request::is('chart-peity') ? 'active' : '' }}"
                                    href="{{url('chart-peity')}}">Peity Charts</a></li>
                            <li><a class="{{ Request::is('chart-c3') ? 'active' : '' }}" href="{{url('chart-c3')}}">C3
                                    Charts</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-icons" role="tabpanel" aria-labelledby="v-pills-icons-tab">
                        <p>Icons</p>
                        <ul>
                            <li><a class="{{ Request::is('icon-fontawesome') ? 'active' : '' }}"
                                    href="{{url('icon-fontawesome')}}">Fontawesome Icons</a></li>
                            <li><a class="{{ Request::is('icon-feather') ? 'active' : '' }}"
                                    href="{{url('icon-feather')}}">Feather Icons</a></li>
                            <li><a class="{{ Request::is('icon-ionic') ? 'active' : '' }}"
                                    href="{{url('icon-ionic')}}">Ionic Icons</a></li>
                            <li><a class="{{ Request::is('icon-material') ? 'active' : '' }}"
                                    href="{{url('icon-material')}}">Material Icons</a></li>
                            <li><a class="{{ Request::is('icon-pe7') ? 'active' : '' }}" href="{{url('icon-pe7')}}">Pe7
                                    Icons</a></li>
                            <li><a class="{{ Request::is('icon-simpleline') ? 'active' : '' }}"
                                    href="{{url('icon-simpleline')}}">Simpleline Icons</a></li>
                            <li><a class="{{ Request::is('icon-themify') ? 'active' : '' }}"
                                    href="{{url('icon-themify')}}">Themify Icons</a></li>
                            <li><a class="{{ Request::is('icon-weather') ? 'active' : '' }}"
                                    href="{{url('icon-weather')}}">Weather Icons</a></li>
                            <li><a class="{{ Request::is('icon-typicon') ? 'active' : '' }}"
                                    href="{{url('icon-typicon')}}">Typicon Icons</a></li>
                            <li><a class="{{ Request::is('icon-flag') ? 'active' : '' }}" href="{{url('icon-flag')}}">Flag
                                    Icons</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-forms" role="tabpanel" aria-labelledby="v-pills-forms-tab">
                        <p>Forms</p>
                        <ul>
                            <li><a class="{{ Request::is('form-basic-inputs') ? 'active' : '' }}"
                                    href="{{url('form-basic-inputs')}}">Basic Inputs </a></li>
                            <li><a class="{{ Request::is('form-input-groups') ? 'active' : '' }}"
                                    href="{{url('form-input-groups')}}">Input Groups </a></li>
                            <li><a class="{{ Request::is('form-horizontal') ? 'active' : '' }}"
                                    href="{{url('form-horizontal')}}">Horizontal Form </a></li>
                            <li><a class="{{ Request::is('form-vertical') ? 'active' : '' }}"
                                    href="{{url('form-vertical')}}"> Vertical Form </a></li>
                            <li><a class="{{ Request::is('form-mask') ? 'active' : '' }}" href="{{url('form-mask')}}"> Form
                                    Mask </a></li>
                            <li><a class="{{ Request::is('form-validation') ? 'active' : '' }}"
                                    href="{{url('form-validation')}}"> Form Validation </a></li>
                            <li><a class="{{ Request::is('form-select2') ? 'active' : '' }}"
                                    href="{{url('form-select2')}}">Form Select2 </a></li>
                            <li><a class="{{ Request::is('form-fileupload') ? 'active' : '' }}"
                                    href="{{url('form-fileupload')}}">File Upload </a></li>
                            <li><a class="{{ Request::is('horizontal-timeline') ? 'active' : '' }}"
                                    href="{{url('horizontal-timeline')}}">Horizontal Timeline</a></li>
                            <li><a class="{{ Request::is('form-wizard') ? 'active' : '' }}"
                                    href="{{url('form-wizard')}}">Form Wizard</a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-tables" role="tabpanel" aria-labelledby="v-pills-tables-tab">
                        <p>Tables</p>
                        <ul>
                            <li><a class="{{ Request::is('tables-basic') ? 'active' : '' }}"
                                    href="{{url('tables-basic')}}">Basic Tables </a></li>
                            <li><a class="{{ Request::is('data-tables') ? 'active' : '' }}"
                                    href="{{url('data-tables')}}">Data Table </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-documentation" role="tabpanel"
                        aria-labelledby="v-pills-documentation-tab">
                        <p>Documentation</p>
                        <ul>
                            <li><a href="#">Documentation </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-changelog" role="tabpanel"
                        aria-labelledby="v-pills-changelog-tab">
                        <p>Change Log</p>
                        <ul>
                            <li><a href="#"><span>Change Log</span> <span class="badge badge-primary ms-auto">v4.0</span>
                                </a></li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="v-pills-multilevel" role="tabpanel"
                        aria-labelledby="v-pills-multilevel-tab">
                        <p>Multi Level</p>
                        <ul>
                            <li class="sub-menu">
                                <a href="javascript:void(0);">Level 1 <span class="menu-arrow"></span></a>
                                <ul class="ms-3">
                                    <li class="sub-menu">
                                        <a href="javascript:void(0);">Level 1 <span class="menu-arrow"></span></a>
                                        <ul>
                                            <li><a href="javascript:void(0);">Level 2</a></li>
                                            <li><a href="javascript:void(0);">Level 3</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="javascript:void(0);">Level 2</a></li>
                            <li><a href="javascript:void(0);">Level 3</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Two Col Sidebar -->
@endif