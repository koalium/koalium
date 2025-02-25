import { getQueryArg } from '@wordpress/url'
import TemplatesOverview from "../templates/TemplatesOverview";

const InitialRouting = () => {
  const queryString = window.location.search
  const page = getQueryArg(queryString, 'page')

  switch (page) {
    case 'call-now-button-templates':
      return <TemplatesOverview />
    default:
      return <TemplatesOverview />
  }
}

export default InitialRouting