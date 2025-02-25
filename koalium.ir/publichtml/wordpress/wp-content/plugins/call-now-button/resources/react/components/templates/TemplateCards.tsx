import {Template} from "../../types/types";
import TemplateCard from "./TemplateCard";
import {ActionTypes, DisplayModes} from "../../types/aux-types";

type TemplateCardsProps =  {
    templates: Template[]
    setTemplate: (template: Template) => void
    types: ActionTypes
    displayModes: DisplayModes
}

/**
 * Pass in the templates
 *
 * Render a Template Card each
 */
const TemplateCards = ({templates, setTemplate, types, displayModes}: TemplateCardsProps) => {
    return <section className="cnb-grid cnb-grid-4columns">{templates.map((template) => {
        return <TemplateCard key={template.id} template={template} types={types} displayModes={displayModes} handleSelect={(template) => setTemplate(template)} />
    })}</section>
}

export default TemplateCards
