import setupReactComponent from './setup-app';
import QueryHistory from './components/database/query-history';
import ArticlesTable from './components/articles-table';
import ModulesList from './components/modules/list';
import DialogList from './components/dialog-list';
import UsersTable from './components/users-table';
import Comments from './components/comments';
import Events from './components/events';
import Menu from './components/menu';

setupReactComponent('dialog-list', DialogList);
setupReactComponent('article-comments', Comments);
setupReactComponent('query-history', QueryHistory);
setupReactComponent('articles', ArticlesTable);
setupReactComponent('modules', ModulesList);
setupReactComponent('users', UsersTable);
setupReactComponent('events', Events);
setupReactComponent('menu', Menu);