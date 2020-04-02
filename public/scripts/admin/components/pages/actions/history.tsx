import React from 'react';
import { SortOrder } from '../../table/table';
import Label from '../../label';
import Duration from '../../table/duration';
import ActionStatus from './status';
import { isNil, isEmpty, map } from 'lodash';
import Parameter from '../../parameter';
import Status, { Type } from '../../status';
import History from '../../stats/history';

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
    secondDuration: number,
    time: string
}

class ActionHistory extends React.Component {
    public render(): React.ReactNode {
        return <History
            breadcrumbsNamePart="Действия"
            apiDataUrl="/api/stats/actions/history"
            tableBuilder={{
                headers: ['Class', 'Duration', 'Status', 'Time'],
                defaultSortColumnIndex: 3,
                defaultSortOrder: SortOrder.DESC,
                buildPureValuesToSort: (action: ActionStat) => [
                    action.class,
                    action.secondDuration,
                    this.isFail(action) ? 0 : (this.isFatal(action) ? -1 : 1),
                    action.time
                ],
                buildRowCells: (action: ActionStat) => [
                    <>{action.class}&nbsp;{action.isAjax && <Label color="yellow">ajax</Label>}</>,
                    <Duration>{`${action.secondDuration.toString()} sec`}</Duration>,
                    <ActionStatus {...action} />,
                    action.time
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
                    if (this.isFail(action)) return [{
                        content: <Status
                            type={Type.WARNING}
                            name="Validation error codes: "
                            message={action.data.errors.join(', ')}
                        />
                    }];
                    if (this.isFatal(action)) return [{
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

    private isFail(action): boolean {
        return this.isFatal(action) && action.data.errors.length !== 0;
    }

    private isFatal(action): boolean {
        return action.responseType === ActionResponseType.ERROR;
    }
}

export default ActionHistory;