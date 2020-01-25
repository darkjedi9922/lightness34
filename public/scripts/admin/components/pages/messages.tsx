import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import { decodeHTML } from 'buk';
import MessageList from '../message-list';

interface User {
    id: number,
    login: string
}

interface MessagesPageProps {
    me: User,
    user: User
}

class MessagesPage extends React.Component<MessagesPageProps> {
    public render(): React.ReactNode {
        return (<>
            <div className="content__header">
                <Breadcrumbs items={[{
                    name: 'Профиль',
                    link: `/admin/users/profile/${this.props.me.login}`
                }, {
                    name: 'Диалоги',
                    link: '/admin/profile/dialogs'
                }, {
                    name: decodeHTML(this.props.user.login),
                    link: `/admin/users/profile/${this.props.user.login}`
                }]} />
            </div>
            <MessageList userId={this.props.user.id} />
        </>);
    }
}

export default MessagesPage;