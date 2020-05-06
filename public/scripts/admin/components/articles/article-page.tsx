import React from 'react';
import Table from '../table/table';
import UserCell from '../table/user-cell';
import { decodeHTML } from 'buk';

interface Author {
    login: string,
    avatarUrl: string,
    isOnline: boolean
}

interface ArticleProps {
    title: string,
    author: Author,
    text: string,
    date: string
}

function ArticlePage(props: ArticleProps): JSX.Element {
    return <>
        <div className="box box--table">
            <Table
                className="article"
                headers={['Автор', 'Название', 'Дата']}
                items={[{
                    cells: [
                        <UserCell
                            login={props.author.login}
                            avatarUrl={props.author.avatarUrl}
                            isOnline={props.author.isOnline}
                        />,
                        decodeHTML(props.title),
                        props.date
                    ],
                    details: [{
                        content: <span className="article__text">{decodeHTML(props.text)}</span>
                    }]
                }]}
            />
        </div>
    </>;
}

export default ArticlePage;