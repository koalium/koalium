import {useEffect, useState} from '@wordpress/element'

import TemplateInput from "./TemplateInput";
import {Template} from "../../types/types";
import TemplateCards from "./TemplateCards";
import {ActionTypes, DisplayModes} from "../../types/aux-types";
import {CnbDomain} from "../../types/cnb-web-types";

const cnb_templates_init = () => {
  // @ts-ignore (This is a WordPress global method, via templates.js)
  window.cnb_templates_init()
}

const templateHashKey = '#t='
const TemplatesOverview = () => {
  const [templates, setTemplates] = useState<Template[] | undefined>(undefined)
  const [selectedTemplate, setSelectedTemplate] = useState<Template | undefined>(undefined)

  const [nonce, setNonce] = useState<string | undefined>(undefined)
  const [ajaxUrl, setAjaxUrl] = useState<string | undefined>(undefined)
  const [types, setTypes] = useState<ActionTypes | undefined>(undefined)
  const [displayModes, setDisplayModes] = useState<DisplayModes | undefined>(undefined)

  const [currentDomain, setCurrentDomain] = useState<CnbDomain | undefined>(undefined)
  const [upgradeLink, setUpgradeLink] = useState<string | undefined>(undefined)

  const templatesInit = (e) => {
    setTemplates(e.detail.templates)
    setNonce(e.detail.nonce)
    setAjaxUrl(e.detail.ajaxUrl)
    setTypes(e.detail.actionTypes)
    setDisplayModes(e.detail.displayModes)
    setCurrentDomain(e.detail.currentDomain)
    setUpgradeLink(e.detail.upgradeLink)

    // get part after "cnb_template="
    const s = window.location.hash.replace(templateHashKey, "")
    // get template and if found, set it
    const t = templates?.find(t => t && t.id === s)
    if (t) setSelectedTemplate(t)
  }

  const handleSetSelectedTemplate = (template: Template) => {
    if (!template || !template?.id) {
      // empty it out if no template is found
      window.location.hash = ''
      setSelectedTemplate(undefined)
      return
    }

    // Template found, use it
    window.location.hash = templateHashKey + template.id
    setSelectedTemplate(template)
  }

  useEffect(() => {
    window.addEventListener('cnb-templates-init', templatesInit, {once: true})

    // Since we are using "domReady" now - we may need to (re)trigger the init call,
    // since it can be fired before this component is ready.
    const timeout = setTimeout(() => {
      cnb_templates_init()
    }, 200)
    return () => {
      clearTimeout(timeout)
      window.removeEventListener('cnb-templates-init', templatesInit)
    }
  }, [])

  if (!templates) {
    return <div>
      Loading the Templates...
      <a onClick={cnb_templates_init}>If nothing happens for a while, click here</a>
    </div>
  }

  return (
    <div className="cnb-templates">
      {selectedTemplate
        ? <TemplateInput
          template={selectedTemplate}
          types={types}
          displayModes={displayModes}
          setTemplate={handleSetSelectedTemplate}
          nonce={nonce}
          ajaxUrl={ajaxUrl}
          domain={currentDomain}
          upgradeLink={upgradeLink}/>
        : <TemplateCards templates={templates} types={types} displayModes={displayModes}
                         setTemplate={handleSetSelectedTemplate}/>
      }
    </div>
  );
}

export default TemplatesOverview;
