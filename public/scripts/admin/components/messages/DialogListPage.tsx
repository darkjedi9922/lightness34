import React from 'react';
import { decodeHTML } from 'buk';
import Breadcrumbs from '../common/Breadcrumbs';
import classNames from 'classnames';
import Table from '../table/Table';
import Mark from '../common/Mark';
import UserCell from '../users/UserCell';
import ButtonCell from '../table/ButtonCell';

interface Message {
    text: string,
    date: string
}

interface Dialog {
    newCount: number,
    sentCount: number
    whoId: number,
    whoAvatar: string,
    whoLogin: string,
    lastMessage: Message,
    deleteUrl: string
}

interface User {
    id: number,
    login: string
}

interface Props {
    countAll: number,
    list: Dialog[],
    pageCount: number,
    pagerHtml: string,
    userMe: User
}

class DialogListPage extends React.Component<Props> {
    public render(): React.ReactNode {
        const newCount = this.props.list
            .filter((dialog) => dialog.newCount !== 0)
            .length;
        return (
            <div className="dialogs">
                <div className="content__header">
                    <div className="breadcrumbs-wrapper">
                        <Breadcrumbs items={[
                            { 'name': 'Профиль', 'link': `/admin/users/profile/${this.props.userMe.login}`},
                            { 'name': `Диалоги (${this.props.countAll})` }
                        ]} />
                        <span className={classNames(
                            "content__count",
                            { "content__count--red": newCount !== 0 }
                        )}>
                            <i className="icon-flash-1"></i>
                            {newCount}
                        </span>
                    </div>
                    {this.props.pageCount > 1 && <div dangerouslySetInnerHTML={{__html: this.props.pagerHtml}}></div>}
                </div>
                <div className="box box--table">
                    <Table
                        className="users"
                        headers={['User', 'Last message', 'Status', 'Last message date', '']}
                        items={this.props.list.map((dialog) => ({
                            cells: [
                                <UserCell login={dialog.whoLogin} avatarUrl={dialog.whoAvatar} />,
                                <span className="dialogs__message-preview">
                                    {decodeHTML(dialog.lastMessage.text)}
                                    &nbsp;
                                    <ButtonCell href={`/admin/profile/dialog?uid=${dialog.whoId}`} icon="email">Перейти</ButtonCell>
                                </span>,
                                dialog.newCount !== 0
                                ? <Mark color="red" label={`${dialog.newCount} new`} className="dialogs__status"/>
                                : (
                                    dialog.sentCount !== 0
                                    ? <Mark color="green" label={`${dialog.sentCount} sent`} className="dialogs__status" />
                                    : <Mark color="grey" label="Readed" className="dialogs__status"/>
                                ),
                                <span className="table__date">{dialog.lastMessage.date}</span>,
                                <ButtonCell href={dialog.deleteUrl} color="red" icon="trash">Очистить</ButtonCell>,
                            ]
                        }))}
                    />
                </div>
            </div>
        );
    }
}

export default DialogListPage;