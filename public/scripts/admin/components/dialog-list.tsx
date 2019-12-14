import React from 'react';
import MessageList from './message-list';
import { decodeHTML } from 'buk';

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

interface DialogListProps {
    countAll: number,
    list: Dialog[],
    pageCount: number,
    pagerHtml: string
}

class DialogList extends React.Component<DialogListProps> {
    private messageListRef: React.RefObject<MessageList>;
    private pagerRef: React.RefObject<HTMLDivElement>;

    constructor(props: DialogListProps) {
        super(props);

        this.messageListRef = React.createRef();
        this.pagerRef = React.createRef();

        this.handleDialogClick = this.handleDialogClick.bind(this);
    }

    public componentDidMount() {
        if (this.props.pageCount > 1) 
            this.pagerRef.current.innerHTML = this.props.pagerHtml;
    }

    public render(): React.ReactNode {
        return (
        <div className="content__row">
            <div className="box dialog-list__dialogs">
                {this.props.countAll === 0 &&
                    <span className="warning">Сообщений пока нет</span>
                }
                <div className="dialogs">
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
                {this.props.pageCount > 1 && <div ref={this.pagerRef}></div>}
            </div>
            <MessageList ref={this.messageListRef}/>
        </div>);
    }

    private handleDialogClick(withWhoId: number) {
        this.messageListRef.current.open(withWhoId);
    }
}

export default DialogList;