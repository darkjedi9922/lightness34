import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import { decodeHTML } from 'buk';
import MessageList from './MessageList';

interface User {
    id: number,
    login: string
}

interface Props {
    me: User,
    user: User,
    pagenumber: number
}

class MessageListPage extends React.Component<Props> {
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
            <MessageList
                userId={this.props.user.id}
                myId={this.props.me.id}
                pagenumber={this.props.pagenumber}
            />
        </>);
    }
}

export default MessageListPage;