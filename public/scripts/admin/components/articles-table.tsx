import React from 'react'
import Table from './table/table';
import { TableItem } from './table/item';
import { decodeHTML } from 'buk'

interface Article {
    id: number,
    title: string,
    author: string,
    date: string
}

interface ArticlesTableProps {
    items: Article[]
}

class ArticlesTable extends React.Component<ArticlesTableProps> {
    public render(): React.ReactNode {
        const items: TableItem[] = [];
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
                    article.date
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

export default ArticlesTable;