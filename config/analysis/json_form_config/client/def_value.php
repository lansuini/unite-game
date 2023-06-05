<?php
return [
    'single' => '
    {
        "Monitor": {
            "report": "1"
        },
        "ServerPostType": {
            "total": "0/200/10/0/350",
            "redirect(single)": "1/200/10/0/350"
        },
        "ServerRequestType": {
            "total": "1/500/10/1/350",
            "CashGet": "1/500/10/1/350",
            "VerifySession": "1/500/10/1/350",
            "CashTransferInOut": "1/500/10/1/350"
        }
    }',
    'transfer' => '
    {
        "Monitor": {
            "report": "1"
        },
        "ServerPostType": {
            "total": "1/200/10/100/350",
            "redirect": "1/200/10/100/350",
            "loginGame": "1/200/10/100/350",
            "transferIn": "1/200/10/100/350",
            "transferOut": "1/200/10/100/350",
            "getPlayerWallet": "1/200/10/100/350"
        },
        "ServerRequestType": {
            "total": "0/500/10/1/350",
            "VerifySession(Transfer)": "1/500/10/1/350"
        }
    }',
];