import React from 'react';
import MessageList from './message-list';

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
    private pagerRef: React.RefObject<HTMLDivElement>;

    constructor(props: DialogListProps) {
        super(props);
        this.pagerRef = React.createRef();
    }

    public componentDidMount() {
        if (this.props.pageCount > 1) 
            this.pagerRef.current.innerHTML = this.props.pagerHtml;
    }

    public render(): React.ReactNode {
        return (
        <div className="content__row">
            <div className="box">
                {this.props.countAll === 0 &&
                    <span className="warning">Сообщений пока нет</span>
                }
                <div className="dialogs">
                    <div className="dialogs__list">
                        {this.props.list.map((dialog, index) => 
                        <div key={index} className="dialogs__item dialog">
                            <div className="dialog__header">
                                <span className="dialog__date">{dialog.lastMessage.date}</span>
                                <a href={"/admin/profile/dialog?with=" + dialog.whoId}
                                    className="dialog__text">{dialog.lastMessage.text}</a>
                            </div>
                            <div className="dialog__info">
                                <div className={"dialog__status" + 
                                    (dialog.newCount !== 0 ? " dialog__status--new" : 
                                    (dialog.activeCount != 0 ? " dialog__status--active" :
                                    "Все сообщения прочитаны"))}
                                >
                                    <i className="dialog__status-icon fontello icon-ok"></i>
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
            <MessageList/>
        </div>);
    }
}

export default DialogList;