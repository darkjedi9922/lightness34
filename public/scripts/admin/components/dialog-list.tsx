import React from 'react';
import MessageList from './message-list';
import { decodeHTML } from 'buk';
import Breadcrumbs from './common/Breadcrumbs';

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

interface DialogListProps {
    countAll: number,
    list: Dialog[],
    pageCount: number,
    pagerHtml: string,
    userMe: User
}

class DialogList extends React.Component<DialogListProps> {
    private messageListRef = React.createRef<MessageList>();
    private dialogsRef = React.createRef<HTMLDivElement>();

    constructor(props: DialogListProps) {
        super(props);

        this.handleDialogClick = this.handleDialogClick.bind(this);
    }

    public componentDidMount() {
        if (this.props.pageCount > 1)
            this.dialogsRef.current.innerHTML += this.props.pagerHtml;
    }

    public render(): React.ReactNode {
        return (
        <div className="content__row">
            <div className="content__column">
                <div className="content__header">
                    <Breadcrumbs items={[{
                        'name': 'Профиль',
                        'link': `/admin/users/profile/${this.props.userMe.login}`
                    }, {
                        'name': 'Диалоги'
                    }]} />
                </div>
                <div className="box">
                    {this.props.countAll === 0 &&
                        <span className="warning">Сообщений пока нет</span>
                    }
                    <div className="dialogs" ref={this.dialogsRef}>
                        <div className="dialogs__list">
                            {this.props.list.map((dialog, index) => 
                            <div key={index} className="dialogs__item dialog" 
                                onClick={() => this.handleDialogClick(dialog.whoId)}
                            >
                                <div className="dialog__header">
                                    <span className="dialog__date">{dialog.lastMessage.date}</span>
                                    <span className="dialog__text">{decodeHTML(dialog.lastMessage.text)}</span>
                                </div>
                                <div className="dialog__info">
                                    <div className={"dialog__status" + 
                                        (dialog.newCount !== 0 ? " dialog__status--new" : 
                                        (dialog.activeCount != 0 ? " dialog__status--active" :
                                        ""))}
                                    >
                                        <i className={"dialog__status-icon fontello" + 
                                            (dialog.newCount !== 0 ? " icon-email" : " icon-ok")}
                                        ></i>
                                        <span className="dialog__status-text">
                                            {dialog.newCount !== 0 ? "Новых: " + dialog.newCount : 
                                                (dialog.activeCount != 0 ? "Непрочитанных: " + dialog.activeCount :
                                                "Все сообщения прочитаны")
                                            }
                                        </span>
                                    </div>
                                    <span className="dialog__who">{dialog.whoLogin}</span>
                                </div>
                            </div>)}
                        </div>
                    </div>
                </div>
            </div>
            <MessageList ref={this.messageListRef} />
        </div>);
    }

    private handleDialogClick(withWhoId: number) {
        this.messageListRef.current.open(withWhoId);
    }
}

export default DialogList;