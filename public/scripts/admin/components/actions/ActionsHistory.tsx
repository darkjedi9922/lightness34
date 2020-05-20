import React from 'react';
import { isNil, isEmpty, map } from 'lodash';
import HistoryPage from '../stats/HistoryPage';
import { SortOrder } from '../table/Table';
import Label from '../common/label';
import DurationCell from '../table/DurationCell';
import Parameter from '../common/Parameter';
import Status, { Type } from '../common/Status';

export enum ActionResponseType {
    JSON = 'json',
    ERROR = 'error',
    REDIRECT = 'redirect'
}

interface PostValue {
    field: string,
    value: string,
    isSecret: boolean
}

interface UploadedFile {
    field: string,
    data?: {
        originalName: string,
        mime?: string,
        tempName?: string,
        status: string,
        size: string
    }
}

interface ActionData {
    data: {
        get: object,
        post: PostValue[],
        files: UploadedFile[]
    },
    errors: number[],
    result: object
}

export interface ActionStat {
    class: string,
    data: ActionData,
    responseType: ActionResponseType,
    responseInfo?: string,
    isAjax: boolean,
    status: number,
    secondDuration: number,
    time: string
}

enum ActionStatus {
    SUCCESS = 0,
    FAILURE = 1,
    FATAL = 2
}

class ActionHistory extends React.Component {
    private statusLabels = {
        0: ['Success', 'ok'],
        1: ['Failure', 'warning'],
        2: ['Fatal', 'error']
    };

    public render(): React.ReactNode {
        return <HistoryPage
            breadcrumbsNamePart="Действия"
            apiDataUrl="/api/stats/actions/history"
            tableBuilder={{
                headers: ['Class', 'Duration', 'Status', 'Time'],
                defaultSortColumnIndex: 3,
                defaultSortOrder: SortOrder.DESC,
                mapHeadersToSortFields: [
                    'class',
                    'duration_sec',
                    'status',
                    'action_id'
                ],
                buildRowCells: (action: ActionStat) => [
                    <>{action.class}&nbsp;{action.isAjax && <Label color="yellow">ajax</Label>}</>,
                    <DurationCell>{`${action.secondDuration.toString()} sec`}</DurationCell>,
                    <span className={`routes__code routes__code--status routes__code--${this.statusLabels[action.status][1]}`}>
                        {this.statusLabels[action.status][0]}
                    </span>,
                    <span className="routes__time">{action.time}</span>
                ],
                buildRowDetails: (action: ActionStat) => [{
                    title: 'Get Data',
                    content: isEmpty(action.data.data.get)
                        // Скорее всего это условие никогда не выполнится, потому что каждый экшн имеет как минимум поле с 
                        // пустым идентификатором. Но на всякий, если вдруг это изменится, оставлю такой случай тут, чтобы не
                        // нужно было вспоминать потом добавить это. Или может быть такое, что экшн просто запущен вручную. 
                        ? <Parameter value="No data" empty={true} />
                        : map(action.data.data.get, (value, field) => {
                            return <Parameter name={field} value={value} />
                        })
                }, {
                    title: 'Post Data',
                    content: isEmpty(action.data.data.post)
                        ? <Parameter value="No data" empty={true} />
                        : map(action.data.data.post, (data) => {
                            return <Parameter name={data.field} value={data.value} empty={data.isSecret} />
                        })
                }, ...(() => {
                    if (isEmpty(action.data.data.files)) return [{
                        title: 'Uploaded Files',
                        content: <Parameter value="No files uploaded" empty={true}
                        />
                    }];
                    else return map(action.data.data.files, (file) => ({
                        title: `Uploaded file - ${file.field}`,
                        content: isNil(file.data)
                            ? <Parameter value="Empty" empty={true} />
                            : <>
                                <Parameter name="name" value={file.data.originalName} />
                                <Parameter
                                    name="mime type"
                                    value={file.data.mime || 'file was not successfully loaded'}
                                    empty={!file.data.mime} />
                                <Parameter
                                    name="temporary path"
                                    value={file.data.tempName || 'file was not successfully loaded'}
                                    empty={!file.data.tempName} />
                                <Parameter name="status" value={file.data.status} />
                                <Parameter name="size" value={file.data.size} />
                            </>
                    }))
                })(), {
                    title: 'Result data',
                    content: isEmpty(action.data.result)
                        ? <Parameter value="No result data" empty={true} />
                        : map(action.data.result, (value, key) => <Parameter name={key} value={value} />)
                }, ...(() => {
                    if (action.status === ActionStatus.FAILURE) return [{
                        content: <Status
                            type={Type.WARNING}
                            name="Validation error codes: "
                            message={action.data.errors.join(', ')}
                        />
                    }];
                    if (action.status === ActionStatus.FATAL) return [{
                        content: <Status
                            type={Type.ERROR}
                            name="Fatal error: "
                            message={action.responseInfo || 'The error was not specified'}
                            emptyMessage={!action.responseInfo}
                        />
                    }];
                    return [];
                })(), ...(() => {
                    if (action.responseType === ActionResponseType.REDIRECT) return [{
                        content: <Status
                            type={Type.OK}
                            name="Redirect: "
                            message={action.responseInfo}
                        />
                    }]
                    return [];
                })()],
            }}
        />
    }
}

export default ActionHistory;