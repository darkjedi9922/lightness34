import React from 'react'
import ReactDOM from 'react-dom'
import $ from 'jquery'

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
}

interface MessageListState {
    users: { [id: number]: User },
    list: Message[],
    loadedPages: number
}

class MessageList extends React.Component<{}, MessageListState> {
    private textAreaIsEmpty: boolean = true;
    private withWhoId: number;
    
    public constructor(props) {
        super(props);

        this.state = {
            users: [],
            list: [],
            loadedPages: 0
        };

        this.handleTextAreaFocus = this.handleTextAreaFocus.bind(this);
        this.handleTextAreaBlur = this.handleTextAreaBlur.bind(this);
    }

    public componentDidMount() {
        const rootEl = $(ReactDOM.findDOMNode(this) as HTMLDivElement);
        rootEl.css({ 'max-width': rootEl.css('width') })
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
            <div className="box">
                <form action="" className="box-form">
                    <span className="box-form__title">Новое сообщение</span>
                    <div 
                        contentEditable={true}
                        className="box-form__textarea" 
                        onFocus={this.handleTextAreaFocus}
                        onBlur={this.handleTextAreaBlur}
                        data-placeholdered
                    >Текст сообщения</div>
                    <button className="box-form__button">Отправить
                        <i className="box-form__button-icon fontello icon-ok"></i></button>
                </form>
            </div>
            {this.state.loadedPages > 0 && 
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
                                                    <i className="message__status-icon fontello icon-ok"></i>
                                                    <span className="message__status-text">{
                                                        message.to_id === this.withWhoId ? "Отправлено" : "Новое"
                                                    }</span>
                                                </div>
                                            }
                                    </div>
                                    <span className="message__text">{message.text}</span>
                                </div>
                            )}
                        </div>
                    }
                </div>
            }
        </div>);
    }

    private handleTextAreaFocus(event: React.FocusEvent<HTMLDivElement>) {
        if (this.textAreaIsEmpty) {
            event.target.textContent = "";
            event.target.removeAttribute('data-placeholdered');
        }
    }

    private handleTextAreaBlur(event: React.FocusEvent<HTMLDivElement>) {
        if (event.target.textContent === "") {
            this.textAreaIsEmpty = true;
            event.target.textContent = "Текст сообщения";
            event.target.setAttribute('data-placeholdered', '');
        } else {
            this.textAreaIsEmpty = false;
        }
    }

    private loadDialogMessages(page: number) {
        $.ajax({
            url: '/api/profile/dialog?withId=' + this.withWhoId + '&p=' + page,
            success: (data: string) => {
                const result: APIListResult = JSON.parse(data);
                this.setState((state) => ({
                    users: result.users,
                    list: result.list,
                    loadedPages: state.loadedPages + 1
                }));
           }
        });
    }
}

export default MessageList;