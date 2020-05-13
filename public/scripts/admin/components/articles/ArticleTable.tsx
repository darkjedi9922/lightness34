import React from 'react'
import Table from '../table/Table';
import { TableItemData } from '../table/TableItem';
import { decodeHTML } from 'buk'

interface Article {
    id: number,
    title: string,
    author: string,
    date: string
}

interface Props {
    items: Article[]
}

class ArticleTable extends React.Component<Props> {
    public render(): React.ReactNode {
        const items: TableItemData[] = [];
        this.props.items.map((article) => {
            items.push({
                cells: [
                    article.id,
                    <a
                        href={`/admin/article?id=${article.id}`}
                        className="table__link"
                    >{decodeHTML(article.title)}</a>,
                    <a
                        href={`/admin/users/profile/${article.author}`}
                        className="table__link"
                    >{decodeHTML(article.author)}</a>,
                    <span className="table__date">{article.date}</span>
                ]
            })
        })
        return (
            <div className="box box--table">
                <Table
                    headers={['ID', 'Название', 'Автор', 'Дата']}
                    items={items}
                ></Table>
            </div>
        )
    }
}

export default ArticleTable;