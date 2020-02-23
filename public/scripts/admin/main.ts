import setupReactComponent from './setup-app';
import QueryHistory from './components/database/query-history';
import QueriesPage from './components/pages/database/queries';
import RouteHistory from './components/pages/routes/history';
import DatabaseTables from './components/database/db-tables';
import ViewsHistory from './components/views/views-history';
import RoutesCharts from './components/pages/routes/charts';
import CashUseHistory from './components/cash/use-history';
import ArticlesTable from './components/articles-table';
import MessagesPage from './components/pages/messages';
import AddUserPage from './components/pages/users/add';
import ProfilePage from './components/pages/profile';
import ModulesList from './components/modules/list';
import DialogsPage from './components/pages/dialogs';
import UsersTable from './components/users-table';
import LogPage from './components/pages/log';
import Comments from './components/comments';
import Form from './components/form/Form';
import Events from './components/events';
import Menu from './components/menu';

setupReactComponent('#cash-use-history', CashUseHistory);
setupReactComponent('#views-stat-page', ViewsHistory);
setupReactComponent('#routes-charts', RoutesCharts);
setupReactComponent('#messages-page', MessagesPage);
setupReactComponent('#route-history', RouteHistory);
setupReactComponent('#query-history', QueryHistory);
setupReactComponent('#article-comments', Comments);
setupReactComponent('#add-user-page', AddUserPage);
setupReactComponent('#profile-page', ProfilePage);
setupReactComponent('#db-tables', DatabaseTables);
setupReactComponent('#dialog-list', DialogsPage);
setupReactComponent('#articles', ArticlesTable);
setupReactComponent('#modules', ModulesList);
setupReactComponent('#queries', QueriesPage);
setupReactComponent('#log-page', LogPage);
setupReactComponent('#users', UsersTable);
setupReactComponent('#events', Events);
setupReactComponent('#menu', Menu);

setupReactComponent('.react-form', Form);