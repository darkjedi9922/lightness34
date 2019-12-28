import setupReactComponent from './setup-app';
import DialogList from './components/dialog-list';
import Comments from './components/comments';
import Events from './components/events';

setupReactComponent('dialog-list', DialogList);
setupReactComponent('article-comments', Comments);
setupReactComponent('events', Events);