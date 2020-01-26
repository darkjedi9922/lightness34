import React from 'react';
import { decodeHTML } from 'buk';
import Breadcrumbs from '../common/Breadcrumbs';
import Table from '../table/table';
import Mark from '../mark';

interface Message {
    text: string,
    date: string
}

interface Dialog {
    newCount: number,
    activeCount: number
    whoId: number,
    whoAvatar: string,
    whoLogin: string,
    lastMessage: Message
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
        return (
            <div className="dialogs">
                <div className="content__header">
                    <Breadcrumbs items={[{
                        'name': 'Профиль',
                        'link': `/admin/users/profile/${this.props.userMe.login}`
                    }, {
                        'name': `Диалоги (${this.props.list.length})`
                    }]} />
                    {this.props.pageCount > 1 &&
                        <div 
                            dangerouslySetInnerHTML={{__html: this.props.pagerHtml}}
                        ></div>
                    }
                </div>
                <div className="box box--table">
                    <Table
                        headers={[
                            'User',
                            'Last message',
                            'Status',
                            'Last message date'
                        ]}
                        items={this.props.list.map((dialog) => ({
                            cells: [
                                <div className="dialogs__user-cell">
                                    <img
                                        className="dialogs__avatar"
                                        src={dialog.whoAvatar}
                                    />
                                    <a
                                        href={`/admin/users/profile/${dialog.whoLogin}`}
                                        className="table__link"
                                    >{dialog.whoLogin}</a>
                                </div>,
                                decodeHTML(dialog.lastMessage.text),
                                dialog.newCount !== 0
                                ? <Mark
                                    color="red"
                                    label={`${dialog.newCount} new`}
                                    className="dialogs__status"
                                />
                                : (
                                    dialog.activeCount !== 0
                                    ? <Mark
                                        color="green"
                                        label={`${dialog.activeCount} sent`}
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
                                </span>
                            ],
                            details: [{
                                content: (
                                    <div className="box-actions">
                                        <a
                                            href={`/admin/profile/dialog?uid=${dialog.whoId}`}
                                            className="box-actions__item"
                                        >
                                            <i className="box-actions__icon icon-email"></i>
                                            Перейти к сообщениям
                                    </a>
                                        <a
                                            href=""
                                            className="box-actions__item box-actions__item--red"
                                        >
                                            <i className="box-actions__icon icon-trash"></i>
                                            Очистить диалог
                                    </a>
                                    </div>
                                )
                            }],
                            detailsIndent: false
                        }))}
                    />
                </div>
            </div>
        );
    }
}

export default DialogsPage;