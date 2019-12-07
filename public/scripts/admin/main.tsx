import React from 'react';
import ReactDOM from 'react-dom';

import MessageList from './components/message-list';

declare const document: Document;

ReactDOM.render(
    <MessageList/>,
    document.getElementById('message-list')
);