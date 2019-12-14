import React from 'react';
import StretchTextarea from './stretch-textarea';
import $ from 'jquery';
import { encodeHTML, decodeHTML } from 'buk';

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
    private textAreaRef = React.createRef<StretchTextarea>();

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
        return (<>
            {this.state.list.length !== 0 &&
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
            }
            <div className="box">
                <div className="box-form">
                    <span className="box-form__title">Добавить комментарий</span>
                    <StretchTextarea
                        ref={this.textAreaRef}
                        placeholder="Текст комментария"
                        className="box-form__textarea"
                    ></StretchTextarea>
                    <button className="box-form__button" onClick={this.handleAddCommentClick}>
                        Добавить
                        <i className="box-form__button-icon fontello icon-ok"></i>
                    </button>
                </div>
            </div>
            {this.props.pagerHtml && 
                <div className="box" 
                    dangerouslySetInnerHTML={{ __html: this.props.pagerHtml }}
                ></div>
            }
        </>);
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

    private handleAddCommentClick(event: React.MouseEvent) {
        const text = this.textAreaRef.current.getText();
        if (!text) return; 

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
                this.textAreaRef.current.empty();
            }
        });
    }
}

export default Comments;