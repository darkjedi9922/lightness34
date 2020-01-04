import React from 'react'
import Table from '../table'
import { TableItem, ItemDetails } from '../table/item'
import { isNil } from 'lodash'
import Parameter from '../parameter';
import Status, { Type } from '../status';

interface Right {
    name: string,
    description: string
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
            const details: ItemDetails[] = [];
            const hasRights = !isNil(module.rights) && module.rights.list.length;
            details.push({
                title: 'Rights',
                content: hasRights
                    ? module.rights.list.map((right, i) => (
                        <Parameter
                            key={i}
                            name={right.name}
                            value={right.description}
                        ></Parameter>
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