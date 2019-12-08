import React from 'react';
import ReactDOM from 'react-dom';

import DialogList from './components/dialog-list';
import MessageList from './components/message-list';

declare const document: Document;
declare const _dialogListData: any;

const dialogListEl = document.getElementById('dialog-list');
if (dialogListEl) {
    ReactDOM.render(
        React.createElement(DialogList, _dialogListData),
        dialogListEl
    );
}

ReactDOM.render(
    <MessageList/>,
    document.getElementById('message-list')
);