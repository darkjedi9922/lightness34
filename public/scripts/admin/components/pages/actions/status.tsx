import React from 'react';
import { ActionStat, ActionResponseType } from './history';
import classNames from 'classnames';

export default function ActionStatus(props: ActionStat): JSX.Element {
    const isFatal = props.responseType === ActionResponseType.ERROR;
    const isSuccess = !isFatal && props.data.errors.length === 0;
    const isFail = !isFatal && !isSuccess;
    return <span className={classNames(
        "routes__code routes__code--status",
        { 'routes__code--ok': isSuccess },
        { 'routes__code--warning': isFail },
        { 'routes__code--error': isFatal }
    )}>{isSuccess ? 'Success' : (isFail ? 'Failure' : 'Fatal')}</span>
}