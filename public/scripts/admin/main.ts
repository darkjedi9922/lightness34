import setupReactComponent from './setup-app';
import DialogList from './components/dialog-list';
import MessageList from './components/message-list';

declare const global: any;

setupReactComponent('dialog-list', DialogList, global._dialogListData);
setupReactComponent('message-list', MessageList);