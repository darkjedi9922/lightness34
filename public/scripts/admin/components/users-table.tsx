import React from 'react';
import Table from './table/table';
import { TableItem } from './table/item';
import { isNil } from 'lodash';
import Status, { Type } from './status';
import UserCell from './table/user-cell';

interface User {
    id: number,
    login: string,
    name?: string,
    surname?: string
    group: string,
    gender: string,
    email?: string,
    avatarUrl: string
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
                    <UserCell login={user.login} avatarUrl={user.avatarUrl} />,
                    (name || surname)
                        ? `${name} ${surname}`
                        : <Status type={Type.NONE} message="None" />,
                    user.group,
                    user.gender,
                    user.email 
                        ? user.email
                        : <Status type={Type.NONE} message="None" />
                ]
            })
        }

        return (
            <div className="box box--table">
                <Table
                    className="users"
                    headers={['Id', 'Логин', 'Имя', 'Группа', 'Пол', 'Email']}
                    items={items}
                ></Table>
            </div>
        );
    }
}

export default UsersTable;