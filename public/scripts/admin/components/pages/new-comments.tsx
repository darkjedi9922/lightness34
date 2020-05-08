import React from 'react';
import ContentHeader, { ContentHeaderGroup } from '../content-header';
import Breadcrumbs from '../common/Breadcrumbs';
import Table from '../table/table';
import UserCell from '../table/user-cell';
import { decodeHTML } from 'buk';
import { isNil } from 'lodash';

interface CommentAuthor {
    login: string,
    avatarUrl: string
}

interface Comment {
    moduleName?: string,
    materialId: number,
    author: CommentAuthor,
    date: string,
    text: string
}

interface NewCommentsProps {
    countAll: number,
    pagerHtml: string,
    comments: Comment[]
}

const NewComments = function(props: NewCommentsProps): JSX.Element {
    return <>
        <ContentHeader>
            <ContentHeaderGroup>
                <Breadcrumbs items={[
                    { name: 'Новое' },
                    { name: `Комментарии (${props.countAll})` }
                ]} />
            </ContentHeaderGroup>
            <ContentHeaderGroup>
                <div dangerouslySetInnerHTML={{ __html: props.pagerHtml }} />
            </ContentHeaderGroup>
        </ContentHeader>
        <div className="box box--table">
            <Table
                headers={['Module', 'Material ID', 'Author', 'Date']}
                items={props.comments.map((comment) => ({
                    cells: [
                        !isNil(comment.moduleName) 
                            ? comment.moduleName
                            : <span className="routes__pagename--index">Unknown</span>,
                        comment.materialId,
                        <UserCell
                            login={comment.author.login}
                            avatarUrl={comment.author.avatarUrl}
                        />,
                        <span className="table__date">{comment.date}</span>
                    ],
                    details: [{
                        content: decodeHTML(comment.text)
                    }]
                }))}
            />
        </div>
    </>;
};

export default NewComments;