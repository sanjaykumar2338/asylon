# Recipients & Departments Guide (Outline)

## Overview
- Alert contacts are per-organization, typed as email or SMS
- Departments control routing for portals

## Departments
- Student portals use student_departments (config)
- Employee portals use employee_departments (config)
- General portal sends to all active contacts

## Managing Contacts
- Add/edit/delete in Admin > Alerts
- Fields: name, value (email/phone), department, active flag
- SMS delivery requires E.164 (+1...) formatting

## Urgent vs Regular
- Urgent alerts go to all eligible contacts
- Reporter follow-up alerts go to alert contacts for that org

## Testing
- Use dev routes or test submissions; check logs for Telnyx skip-SSL warnings in local

## Troubleshooting
- Missing API keys or messaging profile will skip SMS send
- Disable SMS globally with SMS_ENABLED=false for local
