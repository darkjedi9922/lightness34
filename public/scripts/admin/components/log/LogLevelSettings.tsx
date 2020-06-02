import React from 'react';
import { LogLevelSetting } from './_common';
import Mark from '../common/Mark';
import FormCheckbox from '../form/FormCheckbox';

interface Props {
    levels: LogLevelSetting[],
    onLevelSettingChange: (level: LogLevelSetting) => void
}

const LogLevelSettings = function(props: Props) {
    return <div className="box log-level-settings">
        {props.levels.map((level, index) => <div key={index} className="log-level-settings__item">
            <FormCheckbox field={{
                type: 'checkbox',
                name: 'log-level',
                defaultChecked: level.checked,
                onChange: (event) => props.onLevelSettingChange({
                    ...level,
                    checked: event.target.checked
                })
            }} />
            <Mark icon={level.icon} label={level.name} color={level.color} />
        </div>)}
    </div>
}

export default LogLevelSettings;