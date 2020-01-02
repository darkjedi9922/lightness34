import setupReactComponent from './setup-app';
import DialogList from './components/dialog-list';
import UsersTable from './components/users-table';
import Comments from './components/comments';
import Events from './components/events';
import Menu from './components/menu';

setupReactComponent('dialog-list', DialogList);
setupReactComponent('article-comments', Comments);
setupReactComponent('users', UsersTable);
setupReactComponent('events', Events);
setupReactComponent('menu', Menu);