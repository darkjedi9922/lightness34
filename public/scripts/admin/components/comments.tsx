import React from 'react';
import $ from 'jquery';
import { encodeHTML, decodeHTML } from 'buk';
import Form, { TextField } from './form/Form';
import FormTextarea from './form/FormTextarea';

interface User {
    avatarUrl: string
    login: string
};

interface Comment {
    author: User,
    date: string,
    text: string
}

interface CommentsProps {
    me: User,
    // moduleId: number,
    // materialId: number,
    list: Comment[],
    // page: number,
    pagerHtml: string,
    addUrl: string
}

interface CommentsState {
    list: Comment[],
    // loadedPage: number,
    // pagerHtml: string
}

// interface APIListAnswer {
//     list: Comment[],
//     pagerHtml: string
// }

interface APIAddAnswer {
    errors: Array<number>,
    result: {
        id: number,
        date: string
    }
}

class Comments extends React.Component<CommentsProps, CommentsState> {
    public constructor(props: CommentsProps) {
        super(props);

        this.state = {
            list: props.list,
            // loadedPage: 0,
            // pagerHtml: ''
        };

        this.handleAddCommentClick = this.handleAddCommentClick.bind(this);
    }

    // public componentDidMount() {
    //     this.loadPage(this.props.page);
    // }

    public render(): React.ReactNode {
        return (
            <>
                {this.state.list.length !== 0 &&
                    <>
                    <span className="content__title">
                        Комментарии ({this.state.list.length})
                    </span>
                        <div className="box">
                            {this.state.list.map((comment, index) => 
                                <div key={index} className="comment">
                                    <div className="comment__author author">
                                        <div className="author__data">
                                            <img src={comment.author.avatarUrl} className="author__avatar"/>
                                            <div className="author__info">
                                                <a href={"/admin/users/profile/" + comment.author.login}
                                                    className="author__login">{comment.author.login}</a>
                                                <span className="author__date">{comment.date}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="comment__content">{decodeHTML(comment.text)}</div>
                                </div>
                            )}
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
                    <div className="box" 
                        dangerouslySetInnerHTML={{ __html: this.props.pagerHtml }}
                    ></div>
                }
            </>
        );
    }

    // private loadPage(page: number) {
    //     $.ajax({
    //         url: `/api/comments?module_id=${this.props.moduleId}&
    //             material_id=${this.props.materialId}&p=${page}`,
    //         success: (data) => {
    //             const answer: APIListAnswer = JSON.parse(data);
    //             this.setState({
    //                 list: answer.list,
    //                 loadedPage: page,
    //                 pagerHtml: answer.pagerHtml
    //             });
    //         }
    //     })
    // }

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
                            text: encodeHTML(text)
                        }
                    ]
                }));
                form.getField<FormTextarea>('text').empty();
            }
        });
    }
}

export default Comments;