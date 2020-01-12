import React from 'react'
import ReactDOM from 'react-dom'
import $ from 'jquery'
import StretchTextarea from './stretch-textarea';
import { encodeHTML, decodeHTML } from 'buk';

interface Message {
    id: number,
    from_id: number,
    to_id: number,
    date: string,
    readed: boolean,
    text: string
}

interface User {
    login: string,
    avatarUrl: string
}

interface APIListResult {
    users: { [id: number]: User },
    list: Message[],
    addMessageUrl: string;
}

interface APINewMessageResult {
    errors: Array<number>,
    result: {
        id: number,
        date: string
    }
}

interface MessageListState {
    users: { [id: number]: User },
    list: Message[],
    loadedPages: number
}

class MessageList extends React.Component<{}, MessageListState> {
    private withWhoId: number;
    private textAreaRef = React.createRef<StretchTextarea>();
    private addMessageUrl: string;
    
    public constructor(props) {
        super(props);

        this.state = {
            users: [],
            list: [],
            loadedPages: 0
        };

        this.handleSendClick = this.handleSendClick.bind(this);
    }

    public componentDidMount() {
        this.close();
    }

    public open(withWhoId: number) {
        if (withWhoId === this.withWhoId) return;

        this.setState({
            users: [],
            list: [],
            loadedPages: 0
        });

        this.withWhoId = withWhoId;
        const rootEl = $(ReactDOM.findDOMNode(this) as HTMLDivElement);
        rootEl.show();
        this.loadDialogMessages(this.state.loadedPages + 1);
    }

    public close() {
        const rootEl = $(ReactDOM.findDOMNode(this) as HTMLDivElement);
        rootEl.hide();
    }

    public render(): React.ReactNode {
        return (
        <div className="content__column">
            <span className="content__title">Новое сообщение</span>
            <div className="box">
                <div className="box-form">
                    <span className="box-form__title">Новое сообщение</span>
                    <StretchTextarea 
                        ref={this.textAreaRef} 
                        placeholder="Текст сообщения"
                        className="box-form__textarea"
                    ></StretchTextarea>
                    <button className="box-form__button" onClick={this.handleSendClick}>
                        Отправить
                        <i className="box-form__button-icon fontello icon-ok"></i>
                    </button>
                </div>
            </div>
            {this.state.loadedPages > 0 &&
                <>
                    <span className="content__title">Сообщения</span>
                    <div className="box">
                        {this.state.list.length === 0 && this.state.users[this.withWhoId] &&
                            <span className="notice notice--warning notice--fulled">Сообщений с пользователем
                                <span className="notice__strong">{this.state.users[this.withWhoId].login}</span>
                                пока нет</span>
                        }
                        {this.state.list.length !== 0 && 
                            <div className="messages">
                                {this.state.list.map((message, index) => 
                                    <div key={index} className="messages__item message">
                                        <div className="message__header">
                                            <img src={this.state.users[message.from_id].avatarUrl} 
                                                className="message__from-avatar"
                                            />
                                                <div className="message__info">
                                                    <span className="message__from-login">{
                                                        this.state.users[message.from_id].login
                                                    }</span>
                                                    <span className="message__date">{message.date}</span>
                                                </div>
                                                {!message.readed &&
                                                    <div className={"message__status" + (message.to_id === this.withWhoId ?
                                                        " message__status--active" : " message__status--new"
                                                    )}>
                                                        <i className={"message__status-icon fontello " + (message.to_id === this.withWhoId ?
                                                            " icon-ok" : " icon-email"
                                                        )}></i>
                                                        <span className="message__status-text">{
                                                            message.to_id === this.withWhoId ? "Отправлено" : "Новое"
                                                        }</span>
                                                    </div>
                                                }
                                        </div>
                                        <span className="message__text">{decodeHTML(message.text)}</span>
                                    </div>
                                )}
                            </div>
                        }
                    </div>
                </>
            }
        </div>);
    }

    private handleSendClick(event: React.MouseEvent) {
        let text = this.textAreaRef.current.getText();
        console.log(text);
        if (text.length === 0) return;

        $.ajax({
            url: this.addMessageUrl,
            method: "post",
            data: { text: text },
            success: (data) => {
                console.log(data);
                const result: APINewMessageResult = JSON.parse(data);
                this.setState((state) => ({
                    list: [
                        {
                            id: result.result.id,
                            from_id: this.getMyId(),
                            to_id: this.withWhoId,
                            date: result.result.date,
                            readed: false,
                            text: encodeHTML(text)
                        },
                        ...state.list
                    ]
                }));
                this.textAreaRef.current.empty();
            }
        });
    }

    private loadDialogMessages(page: number) {
        $.ajax({
            url: '/api/profile/dialog?withId=' + this.withWhoId + '&p=' + page,
            success: (data: string) => {
                const result: APIListResult = JSON.parse(data);
                this.addMessageUrl = result.addMessageUrl;
                this.setState((state) => ({
                    users: result.users,
                    list: result.list,
                    loadedPages: state.loadedPages + 1
                }));
           }
        });
    }

    private getMyId(): number {
        for (const id in this.state.users) {
            if (this.state.users.hasOwnProperty(id)) {
                const idNumber = id as unknown as number;
                if (idNumber != this.withWhoId) return idNumber;
            }
        }
    }
}

export default MessageList;