import React from 'react';
import { isNil } from 'lodash';
import Label from '../label';

interface UserCellProps {
    login: string,
    avatarUrl: string,
    isOnline?: boolean
}

function UserCell(props: UserCellProps): JSX.Element {
    return <div className="users__user-cell">
        <a
            href={`/admin/users/profile/${props.login}`}
            className="users__avatar-link"
        ><img className="users__avatar" src={props.avatarUrl}/></a>
        <a
            href={`/admin/users/profile/${props.login}`}
            className="table__link"
        >{props.login}</a>&nbsp;
        {!isNil(props.isOnline) && 
            <Label color={props.isOnline ? 'green' : 'red'}>
                {props.isOnline ? 'Online' : 'Offline'}
            </Label>
        }
    </div>
}

export default UserCell;