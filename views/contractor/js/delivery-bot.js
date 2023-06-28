var botui = new BotUI('message-box'),
    customquery='',
    sc1 = 'We can create 4 types of issues Volilation, Observation, Incident, Recognition',
    sc2 = 'Because the jobsite you are looking for might not be assigned to you. Please contact  respected Jobsite  Administrator'
    uc1 = 'No, WT Personnel role requires WT email id.Please contact  respected Jobsite  Administrator'
    uc2 = 'Contractor Employees are not allowed to login into the system.Please contact  respected Jobsite  Administrator for more details';

var welcome = function () {
botui.message
  .bot('Welcome to WT Chat Bot faq. Please select area of your question')
  .then(function () {
    return botui.action.button({
      delay: 1000,
      addMessage: false, // so we could the address in message instead if 'Existing Address'
      action: [{
        text: 'Issue Creation',
        value: 'issuecreation'
      }, {
        text: 'User Creation',
        value: 'usercreation'
      }, {
        text: 'Custom Question',
        value: 'customQuestion'
      }]
    })
}).then(function (res) {
  if(res.value == 'issuecreation') {
    return botui.action.button({
      delay: 1000,
      addMessage: false, // so we could the address in message instead if 'Existing Address'
      action: [{
        text: 'What type of issues we can create',
        value: 'sc1'
      }, {
        text: 'How many type of issues we can create',
        value: 'sc1'
      }, {
        text: 'Iam not able to view a  jobsite at the time of issue creation',
        value: 'sc2'
      }]
      })
  }   if(res.value == 'usercreation') {
    return botui.action.button({
      delay: 1000,
      addMessage: false, // so we could the address in message instead if 'Existing Address'
      action: [{
        text: 'Can I WT Personnel without having an WT email id',
        value: 'uc1'
      }, {
        text: 'Iam contractor Employee, not able to login to the System',
        value: 'uc2'
      }]
      })
  }else {
    botui.message.human({
      delay: 500,
      content: res.text
    });
    askAddress();
  }
}).then(function (res) {
  if(res.value == 'sc1') {
       botui.message.human({
      delay: 500,
      content: sc1
    });
    end();
  }else if(res.value == 'sc2') {
       botui.message.human({
      delay: 500,
      content: sc2
    });
    end();
  }else if(res.value == 'uc1') {
       botui.message.human({
      delay: 500,
      content: uc1
    });
    end();
  }else if(res.value == 'uc2') {
       botui.message.human({
      delay: 500,
      content: uc2
    });
    end();
  }

});
};

var askAddress = function () {
  botui.message
    .bot({
      delay: 500,
      content: 'Please write your question:'
    })
    .then(function () {
      return botui.action.text({
        delay: 1000,
        action: {
          size: 30,
          icon: 'map-marker',
          value: customquery, // show the saved address if any
          placeholder: 'Your query'
        }
      })
    }).then(function (res) {
      botui.message
        .bot({
          delay: 500,
          content: 'Your Query: ' + res.value
        });

      customquery = res.value; // save address

      return botui.action.button({
        delay: 1000,
        action: [{
          icon: 'check',
          text: 'Confirm',
          value: 'confirm'
        }, {
          icon: 'pencil',
          text: 'Edit',
          value: 'edit'
        }]
      })
    }).then(function (res) {
      if(res.value == 'confirm') {
        endforquestion();
      } else {
        askAddress();
      }
    });
}

var end = function () {
  botui.message
    .bot({
      delay: 1000,
      content: 'Thank you. For Contacting WT Chat Bot'
    });
}

var endforquestion = function () {
  botui.message
    .bot({
      delay: 1000,
      content: 'We will back to you in 24hrs, Thank you, For Contacting WT Chat Bot'
    });
}
