import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import Table, { SortOrder } from '../table/Table';
import Status, { Type } from '../common/Status';
import Mark from '../common/Mark';
import classNames from 'classnames';
import ContentHeader from '../content/ContentHeader';
import { isNil } from 'lodash';
import { MarkColor } from '../common/_common';
import LogLevelSettings from './LogLevelSettings';
import { LogLevelSetting } from './_common';

interface LogRecord {
    type: string,
    ip?: string, // depends on cli flag
    cli: boolean,
    time: string,
    message: string
}

interface Props {
    date: string,
    records: LogRecord[],
    readedRecords: number,
    pagerHtml?: string
}

interface State {
    levels: LogLevelSetting[]
}

class LogPage extends React.Component<Props, State> {
    public constructor(props: Props) {
        super(props);
        this.state = {
            levels: [
                { name: 'Emergency', icon: 'clock', color: MarkColor.RED, checked: true },
                { name: 'Critical', icon: 'flash-1', color: MarkColor.RED, checked: true },
                { name: 'Error', icon: 'cancel-circle', color: MarkColor.RED, checked: true },
                { name: 'Alert', icon: 'alert', color: MarkColor.YELLOW, checked: true },
                { name: 'Warning', icon: 'attention', color: MarkColor.YELLOW, checked: true },
                { name: 'Notice', icon: 'bell-alt', color: MarkColor.BLUE, checked: true },
                { name: 'Info', icon: 'info-circled', color: MarkColor.GREEN, checked: true },
                { name: 'Debug', icon: 'bug', color: MarkColor.PURPLE, checked: true },
                { name: 'Testing', icon: 'flag', color: MarkColor.PURPLE, checked: true },
                { name: 'Other', icon: '', color: MarkColor.GREY, checked: true },
            ]
        }

        this.onLogLevelSettingChange = this.onLogLevelSettingChange.bind(this);
    }

    public render(): React.ReactNode {
        const state = this.state;
        const unreadedRecords = this.props.records.length - this.props.readedRecords;
        return (
            <div className="log">
                <ContentHeader>
                    <div className="breadcrumbs-wrapper">
                        <Breadcrumbs items={[{
                            name: `Лог ${this.props.date} (${this.props.records.length})`
                        }]} />
                        <span className={classNames(
                            "content__count",
                            { "content__count--yellow": unreadedRecords }
                        )}>
                            <i className="icon-flash-1"></i>
                            {unreadedRecords}
                        </span>
                    </div>
                    {!isNil(this.props.pagerHtml) && <div dangerouslySetInnerHTML={{ __html: this.props.pagerHtml }}></div>}
                </ContentHeader>
                <LogLevelSettings 
                    levels={this.state.levels}
                    onLevelSettingChange={this.onLogLevelSettingChange}
                />
                <div className="box box--table">
                    <Table
                        headers={['Тип', 'IP', 'Время']}
                        sort={{
                            defaultCellIndex: 2,
                            defaultOrder: SortOrder.DESC,
                            isAlreadySorted: false
                        }}
                        items={this.props.records
                            .filter((record) => this.getRecordLevel(record).checked)
                            .map((record, index) => 
                        ({
                            pureCellsToSort: [record.type, record.ip, index],
                            cells: [
                                <Mark 
                                    className="log__type"
                                    label={record.type} 
                                    color={this.getRecordLevel(record).color}
                                    icon={this.getRecordLevel(record).icon}
                                />,
                                record.cli
                                    ? <Status type={Type.NONE} message="From CLI" />
                                    : record.ip,
                                <>
                                    <span className="log__date">{record.time}</span>
                                    &nbsp;
                                    {index + 1 > this.props.readedRecords
                                        ? <span className="mark mark--red">
                                            Unreaded
                                        </span>
                                        : <span className="mark mark--grey">
                                            Readed
                                        </span>
                                    }
                                </>
                            ],
                            details: [{
                                content: (() => {
                                    let messageParts = record.message.split('\n');
                                    const header = messageParts[0];
                                    messageParts.shift();
                                    const message = messageParts.join('\n');
                                    return (
                                        <div className="log__message">
                                            <p className="log__message--header">
                                                {header}
                                            </p>
                                            {messageParts.length !== 1 && message}
                                        </div>
                                    )
                                })()
                            }]
                        }))}
                    />
                </div>
            </div>
        );
    }

    private onLogLevelSettingChange(level: LogLevelSetting) {
        this.setState((state) => ({
            levels: state.levels.map((current) => {
                if (current.name === level.name) return level;
                return current;
            })
        }))
    }

    private getRecordLevel(record: LogRecord): LogLevelSetting {
        let level = this.state.levels.find((level) => record.type === level.name);
        if (!level) level = this.state.levels[this.state.levels.length - 1]; // Other
        return level;
    }
}

export default LogPage;