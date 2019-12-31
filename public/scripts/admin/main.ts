import setupReactComponent from './setup-app';
import DialogList from './components/dialog-list';
import Comments from './components/comments';
import Events from './components/events';
import Menu from './components/menu';

setupReactComponent('dialog-list', DialogList);
setupReactComponent('article-comments', Comments);
setupReactComponent('events', Events);
setupReactComponent('menu', Menu);