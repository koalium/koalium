import {Template} from "../../types/types";
import {ACTION_TYPE} from "../../types/cnb-web-types";
import {ActionTypes, DisplayModes} from "../../types/aux-types";

type TemplateCardProps = {
    template: Template
    handleSelect: (template: Template) => void
    types: ActionTypes
    displayModes: DisplayModes
}

const getTypeName = (actionType: ACTION_TYPE, types: ActionTypes) => {
    const actionTypeName = String(actionType)
    if (types && actionTypeName in types) {
        return types[actionTypeName].name
    } else {
        return actionTypeName
    }
}

const TemplateCard = ({template, handleSelect, types, displayModes}: TemplateCardProps) => {
    return <article
        onClick={() => handleSelect(template)}
        className="cnb-template-container"
        data-template-id={template.id}>
        <section>
            <header
                className="cnb-relative"
                style={{backgroundImage: "url(" + template.image + ")",}}>
                    {template.categories.includes("pro") &&
                        <span className="cnb-pro-badge">Pro<br/>Required</span>}
                    {displayModes && template.button.options.displayMode &&
                        <span className="cnb-feature-label">{displayModes[template.button.options.displayMode]}</span>}                
            </header>
            <div className="text-block">
                <h3>{template.name}</h3>
                <p>{template.description}</p>
                <p>{template.button.actions.map(a => <span
                  key={a.id}
                    className="cnb-feature-label">{getTypeName(a.actionType, types)}</span>)}</p>
            </div>
        </section>
    </article>
}

export default TemplateCard
