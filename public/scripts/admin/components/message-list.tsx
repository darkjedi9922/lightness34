import React from 'react'
import ReactDOM from 'react-dom'
import $ from 'jquery'
import { encodeHTML, decodeHTML } from 'buk';
import Form, { TextareaField } from './form/Form';
import Table from './table/table';
import { isNil } from 'lodash';
import classNames from 'classnames';
import FormTextarea from './form/FormTextarea';

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
    pagerHtml?: string,
    addMessageUrl: string
}

interface APINewMessageResult {
    errors: Array<number>,
    result: {
        id: number,
        date: string
    }
}

interface MessageListProps {
    userId: number,
    myId: number,
    pagenumber: number
}

interface MessageListState {
    users: { [id: number]: User },
    list: Message[],
    pagerHtml?: string
}

class MessageList extends React.Component<MessageListProps, MessageListState> {
    private withWhoId: number;
    private addMessageUrl: string;
    
    public constructor(props) {
        super(props);

        this.state = {
            users: [],
            list: [],
            pagerHtml: null
        };

        this.handleSendClick = this.handleSendClick.bind(this);
    }

    public componentDidMount() {
        this.open(this.props.userId);
    }

    public open(withWhoId: number) {
        if (withWhoId === this.withWhoId) return;

        this.setState({
            users: [],
            list: []
        });

        this.withWhoId = withWhoId;
        const rootEl = $(ReactDOM.findDOMNode(this) as HTMLDivElement);
        rootEl.show();
        this.loadDialogMessages();
    }

    public render(): React.ReactNode {
        const newCount = this.state.list.filter((message) => 
            message.from_id !== this.props.myId && !message.readed
        ).length;
        return (<>
            <span className="content__title">Новое сообщение</span>
            <div className="box">
                <Form 
                    method="post"
                    fields={[{
                        type: 'textarea',
                        name: 'text',
                        placeholder: 'Текст сообщения',
                        defaultValue: '',
                        minRows: 2
                    } as TextareaField]}
                    buttonText="Отправить"
                    onSubmit={this.handleSendClick}
                />
            </div>
            <div className="content__header">
                <div className="breadcrumbs-wrapper">
                    <span className="content__title">
                        Сообщения ({this.state.list.length})
                    </span>
                    <span className={classNames(
                        "content__count",
                        { "content__count--red": newCount !== 0 }
                    )}>
                        <i className="icon-flash-1"></i>
                        {newCount}
                    </span>
                </div>
                {!isNil(this.state.pagerHtml) &&
                    <div className="content__pager" dangerouslySetInnerHTML={{
                        __html: decodeHTML(this.state.pagerHtml)
                    }}></div>
                }
            </div>
            <div className="box box--table">
                <Table
                    className="dialogs users"
                    headers={['User', 'Date']}
                    items={this.state.list.map((message) => ({
                        cells: [
                            <div className="users__user-cell">
                                <img
                                    className="users__avatar"
                                    src={this.state.users[message.from_id].avatarUrl}
                                />
                                <a
                                    href={`/admin/users/profile/${
                                        this.state.users[message.from_id].login
                                    }`}
                                    className="table__link"
                                >{this.state.users[message.from_id].login}</a>
                            </div>,
                            <>
                                <span className="table__date">{message.date}</span>
                                &nbsp;
                                {message.readed
                                ? <span className="mark mark--grey">Readed</span>
                                : (
                                    message.to_id === this.withWhoId
                                    ? <span className="mark mark--green">Sent</span>
                                    : <span className="mark mark--red">New</span>
                                )}
                            </>
                        ],
                        details: [{
                            content: (
                                <span className="message__text">
                                    {decodeHTML(message.text)}
                                </span>
                            )
                        }]
                    }))}
                />
            </div>
        </>);
    }

    private handleSendClick(
        event: React.FormEvent<HTMLFormElement>,
        form: Form
    ): void {
        const textInput: HTMLTextAreaElement = event.currentTarget.elements['text'];
        let text = textInput.value;
        if (text.length === 0) return;

        $.ajax({
            url: this.addMessageUrl,
            method: "post",
            data: { text: text },
            success: (data) => {
                const result: APINewMessageResult = JSON.parse(data);
                this.setState((state) => ({
                    list: [
                        {
                            id: result.result.id,
                            from_id: this.props.myId,
                            to_id: this.withWhoId,
                            date: result.result.date,
                            readed: this.withWhoId === this.props.myId,
                            text: encodeHTML(text)
                        },
                        ...state.list
                    ]
                }));
                form.getField<FormTextarea>('text').empty();
            }
        });
    }

    private loadDialogMessages() {
        $.ajax({
            url: '/api/profile/dialog?withId=' + this.withWhoId 
                + '&p=' + this.props.pagenumber,
            success: (data: string) => {
                const result: APIListResult = JSON.parse(data);
                this.addMessageUrl = result.addMessageUrl;
                this.setState((state) => ({
                    users: result.users,
                    list: result.list,
                    pagerHtml: result.pagerHtml
                }));
           }
        });
    }
}

export default MessageList;