import React from 'react';
import { decodeHTML } from 'buk';
import Breadcrumbs from '../common/Breadcrumbs';
import classNames from 'classnames';
import Table from '../table/table';
import Mark from '../mark';

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

interface DialogsPageProps {
    countAll: number,
    list: Dialog[],
    pageCount: number,
    pagerHtml: string,
    userMe: User
}

class DialogsPage extends React.Component<DialogsPageProps> {
    public render(): React.ReactNode {
        const newCount = this.props.list
            .filter((dialog) => dialog.newCount !== 0)
            .length;
        return (
            <div className="dialogs">
                <div className="content__header">
                    <div className="breadcrumbs-wrapper">
                        <Breadcrumbs items={[{
                            'name': 'Профиль',
                            'link': `/admin/users/profile/${this.props.userMe.login}`
                        }, {
                            'name': `Диалоги (${this.props.countAll})`
                        }]} />
                        <span className={classNames(
                            "content__count",
                            { "content__count--red": newCount !== 0 }
                        )}>
                            <i className="icon-flash-1"></i>
                            {newCount}
                        </span>
                    </div>
                    {this.props.pageCount > 1 &&
                        <div 
                            dangerouslySetInnerHTML={{__html: this.props.pagerHtml}}
                        ></div>
                    }
                </div>
                <div className="box box--table">
                    <Table
                        className="users"
                        headers={[
                            'User',
                            'Last message',
                            'Status',
                            'Last message date',
                            ''
                        ]}
                        items={this.props.list.map((dialog) => ({
                            cells: [
                                <div className="users__user-cell">
                                    <a 
                                        href={`/admin/users/profile/${dialog.whoLogin}`}
                                        className="users__avatar-link"
                                    >
                                        <img
                                            className="users__avatar"
                                            src={dialog.whoAvatar}
                                        />
                                    </a>
                                    <a
                                        href={`/admin/users/profile/${dialog.whoLogin}`}
                                        className="table__link"
                                    >{dialog.whoLogin}</a>
                                </div>,
                                <span className="dialogs__message-preview">
                                    {decodeHTML(dialog.lastMessage.text)}
                                    &nbsp;
                                    <a
                                        href={`/admin/profile/dialog?uid=${dialog.whoId}`}
                                        className="box-actions__item"
                                    >
                                        <i className="box-actions__icon icon-email"></i>
                                        Перейти
                                    </a>
                                </span>,
                                dialog.newCount !== 0
                                ? <Mark
                                    color="red"
                                    label={`${dialog.newCount} new`}
                                    className="dialogs__status"
                                />
                                : (
                                    dialog.sentCount !== 0
                                    ? <Mark
                                        color="green"
                                        label={`${dialog.sentCount} sent`}
                                        className="dialogs__status"
                                    />
                                    : <Mark
                                        color="grey"
                                        label="Readed"
                                        className="dialogs__status"
                                    />
                                ),
                                <span className="table__date">
                                    {dialog.lastMessage.date}
                                </span>,
                                <a
                                    href={dialog.deleteUrl}
                                    className="box-actions__item box-actions__item--red"
                                >
                                    <i className="box-actions__icon icon-trash"></i>
                                    Очистить
                                </a>
                            ]
                        }))}
                    />
                </div>
            </div>
        );
    }
}

export default DialogsPage;