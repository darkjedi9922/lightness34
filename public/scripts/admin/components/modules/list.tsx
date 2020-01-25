import React from 'react'
import Table from '../table/table'
import { TableItem } from '../table/item'
import { isNil } from 'lodash'
import Parameter from '../parameter'
import Status, { Type } from '../status'
import classNames from 'classnames'

interface Right {
    name: string,
    description: string,
    checkArgs?: string[]
}

interface RightsDesc {
    list: Right[]
}

interface Module {
    name: string,
    class: string,
    parentModuleName?: string,
    rights?: RightsDesc,
}

interface ModulesListProps {
    list: Module[]
}

class ModulesList extends React.Component<ModulesListProps> {
    public render(): React.ReactNode {
        const items: TableItem[] = [];
        this.props.list.map((module) => {
            const details = [];
            const hasRights = !isNil(module.rights) && module.rights.list.length;
            details.push({
                title: 'Rights',
                content: hasRights
                    ? module.rights.list.map((right, i) => (
                        <div key={i} className="module-right">
                            <Parameter
                                name={right.name}
                                value={right.description}
                            ></Parameter>
                            {!isNil(right.checkArgs) && right.checkArgs.length &&
                                <span className="module-right__check">
                                    {right.checkArgs.map((type, i) =>
                                        <span key={i} className={classNames(
                                            "module-right__check-arg",
                                            `module-right__check-arg--${type}`
                                        )}>{type}</span>
                                    )}
                                </span>
                            }
                        </div>
                    ))
                    : <Status
                        type={Type.EMPTY}
                        message='The module has no rights'
                    ></Status>
            })
            items.push({
                cells: [
                    module.name,
                    module.class,
                    module.parentModuleName
                ],
                details
            })
        })

        return (
            <div className="box box--table">
                <Table
                    headers={['Name', 'Class', 'Parent']}
                    items={items}
                ></Table>
            </div>
        )
    }
}

export default ModulesList;