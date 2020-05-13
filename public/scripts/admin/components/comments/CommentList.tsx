import React from 'react';
import $ from 'jquery';
import { encodeHTML, decodeHTML } from 'buk';
import Form, { TextField } from '../form/Form';
import FormTextarea from '../form/FormTextarea';
import Table from '../table/Table';
import UserCell from '../users/UserCell';
import ButtonCell from '../table/ButtonCell';
import { isNil } from 'lodash';

interface User {
    avatarUrl: string
    login: string,
    isOnline: boolean
};

interface Comment {
    author: User,
    date: string,
    text: string,
    isNew: boolean,
    deleteUrl?: string
}

interface Props {
    me: User,
    list: Comment[],
    pagerHtml: string,
    addUrl: string
}

interface State {
    list: Comment[]
}

interface APIAddAnswer {
    errors: Array<number>,
    result: {
        id: number,
        date: string,
        deleteUrl?: string
    }
}

class CommentList extends React.Component<Props, State> {
    public constructor(props: Props) {
        super(props);
        this.state = { list: props.list };
        this.handleAddCommentClick = this.handleAddCommentClick.bind(this);
    }

    public render(): React.ReactNode {
        const headers = ['Автор', 'Дата'];
        const items = [];
        
        this.state.list.map((comment) => {
            const cells = [
                <UserCell
                    login={comment.author.login}
                    avatarUrl={comment.author.avatarUrl}
                    isOnline={comment.author.isOnline}
                />,
                <>
                    <span className="table__date">{comment.date}</span>
                    &nbsp;
                    {comment.isNew && <span className="mark mark--red">New</span>}
                </>
            ];

            if (!isNil(comment.deleteUrl)) cells.push(<ButtonCell
                href={comment.deleteUrl}
                color="red"
                icon="trash"
            >Удалить</ButtonCell>);

            items.push({
                cells,
                details: [{ content: decodeHTML(comment.text) }]
            });
        });

        return (
            <>
                {this.state.list.length !== 0 &&
                    <>
                    <span className="content__title">Комментарии ({this.state.list.length})</span>
                        <div className="box box--table">
                            <Table headers={headers} items={items}/>
                        </div>
                    </>
                }
                <span className="content__title">Добавить комментарий</span>
                <div className="box">
                    <Form
                        method="post"
                        fields={[{
                            type: 'textarea',
                            name: 'text',
                            placeholder: 'Текст комментария'
                        } as TextField]}
                        buttonText="Добавить"
                        onSubmit={this.handleAddCommentClick}
                    />
                </div>
                {this.props.pagerHtml && 
                    <div className="box" dangerouslySetInnerHTML={{ __html: this.props.pagerHtml }}></div>
                }
            </>
        );
    }

    private handleAddCommentClick(
        event: React.FormEvent<HTMLFormElement>,
        form: Form
    ) {
        const textarea: HTMLTextAreaElement = event.currentTarget.elements['text'];
        const text = textarea.value;
        if (text === '') return; 

        $.ajax({
            url: this.props.addUrl,
            method: 'post',
            data: {
                text: text
            },
            success: (data) => {
                const result: APIAddAnswer = JSON.parse(data);
                console.log(result);
                this.setState((state, props) => ({
                    list: [
                        ...state.list,
                        {
                            author: props.me,
                            date: result.result.date,
                            text: encodeHTML(text),
                            isNew: false,
                            deleteUrl: result.result.deleteUrl
                        }
                    ]
                }));
                form.getField<FormTextarea>('text').empty();
            }
        });
    }
}

export default CommentList;