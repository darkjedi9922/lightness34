import React from 'react';
import Table from './table/table';
import { TableItem } from './table/item';
import { isNil } from 'lodash';

interface User {
    id: number,
    login: string,
    name?: string,
    surname?: string
    group: string,
    gender: string,
    email?: string
}

interface Props {
    items: User[]
}

class UsersTable extends React.Component<Props> {
    public render(): React.ReactNode {
        const items: TableItem[] = [];

        for (let i = 0; i < this.props.items.length; i++) {
            const user = this.props.items[i];
            const name = !isNil(user.name) ? user.name : '';
            const surname = !isNil(user.surname) ? user.surname : '';
            items.push({
                cells: [
                    user.id,
                    <a
                        href={`/admin/users/profile/${user.login}`} 
                        className="table__link"
                    >{user.login}</a>,
                    `${name} ${surname}`,
                    user.group,
                    user.gender,
                    !isNil(user.email) ? user.email : ''
                ]
            })
        }

        return (
            <div className="box box--table">
                <Table
                    headers={['Id', 'Логин', 'Имя', 'Группа', 'Пол', 'Email']}
                    items={items}
                ></Table>
            </div>
        );
    }
}

export default UsersTable;