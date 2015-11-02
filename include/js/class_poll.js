
/* $Id: class_poll.js 282 2009-12-18 20:40:41Z john $ */

SocialEngineAPI.Polls = new Class({
  
  Base : {},
  
  
  // Properties
  options: {
    'ajaxURL' : 'poll_ajax.php',
    'maxRequestsInProgress' : 1
  },
  
  requestsInProgress: 0,
  
  currentConfirmDeleteID: 0,
  
  
  
  // Creation
  newPollOption: function()
  {
    var pollOptionContainer = $('sePollOptions');
    var currentPollOptionCount = pollOptionContainer.getElements('input').length;
    var newPollOptionIndex = currentPollOptionCount+1;
    
    if( currentPollOptionCount>=20 )
    {
      alert(this.Base.Language.Translate(2500098));
    }
    
    else
    {
      var newOptionTemplate = $('sePollsOptionTemplate').getElement('.sePollsOption').clone();
      
      newOptionTemplate.setProperty('id', 'sePollsOption_'+newPollOptionIndex);
      newOptionTemplate.getElement('.sePollsIndex').setProperty('html', newPollOptionIndex);
      
      newOptionTemplate.inject(pollOptionContainer);
      newOptionTemplate.focus();
    }
  },
  
  
  
  // Retrieval
  getPollData: function(pollID)
  {
    // One request at a time please
    /*
    if( this.requestsInProgress>=this.options.maxRequestsInProgress )
    {
      alert(SELanguage.Translate(2500115));
      return false;
    }
    */
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'infopoll',
        'poll_id' : pollID
      },
      'onComplete': function(responseObject)
      {
        //bind.requestsInProgress--;
        
        if( !responseObject || $type(responseObject)!="object" || responseObject.result=="failure" )
        {
          if( !responseObject.message )
            alert(bind.Base.Language.Translate(2500114));
          else
            alert(responseObject.message);
        }
        else
        {
          bind.generatePollResults(pollID, responseObject);
          bind.pollViewMode(pollID);
        }
      }
    });
    
    //this.requestsInProgress++;
    request.send();
  },
  
  
  generatePollResults: function(pollID, pollDataObject)
  {
    var pollResultsContainer = $('poll'+pollID+'_results');
    pollResultsContainer.empty();
    
    // Options
    var bind = this;
    var isFirst = true;
    var pollResultIndex = 1;
    pollDataObject.poll_options.each(function(pollOptionLabel, pollOptionIndex)
    {
      var pollResultTemplate = $('pollResultTemplate').getElement('.pollResult').clone();
      
      // Generate Stats
      var pollResultID = "poll" + pollID + "_bar" + pollOptionIndex;
      var pollResultClass = "poll_bar" + (pollResultIndex - (20 * Math.floor(pollResultIndex/20))).toString();
      
      var width = 3;
      var percentage = 0;
      var thisVoteCount = parseInt($try(function(){
        return pollDataObject.poll_answers[pollOptionIndex];
      }, function(){
        return 0;
      }));
      
      width += Math.round((thisVoteCount / pollDataObject.poll_totalvotes) * 400);
      percentage += Math.round((thisVoteCount / pollDataObject.poll_totalvotes) * 100);
      
      // Apply to template
      var votesString = '('+bind.Base.Language.TranslateFormatted(2500028, [thisVoteCount || '0'])+')';

      pollResultTemplate.getElement('.pollResultLabel').setProperty('html', pollOptionLabel);
      pollResultTemplate.getElement('.pollResultBar').addClass(pollResultClass);
      pollResultTemplate.getElement('.pollResultBar').setProperty('id', pollResultID);
      pollResultTemplate.getElement('.pollResultPercentage').setProperty('html', percentage+'%');
      pollResultTemplate.getElement('.pollResultVotes').setProperty('html', votesString);
      
      // Space them
      if( !isFirst )
      {
        (new Element('br')).inject(pollResultTemplate, 'top');
        //(new Element('br')).inject(pollResultTemplate, 'top');
      }
      
      // Inject
      pollResultTemplate.inject(pollResultsContainer);
      
      pollResultIndex++;
      isFirst = false;
    });
    
    // Effects
    pollDataObject.poll_options.each(function(pollOptionLabel, pollOptionIndex)
    {
      var pollResultElement = $("poll" + pollID + "_bar" + pollOptionIndex);
      
      var width = 3;
      var percentage = 0;
      if( pollDataObject.poll_answers[pollOptionIndex] )
        width += Math.round((pollDataObject.poll_answers[pollOptionIndex] / pollDataObject.poll_totalvotes) * 400);
      if( pollDataObject.poll_answers[pollOptionIndex] )
        percentage += Math.round((pollDataObject.poll_answers[pollOptionIndex] / pollDataObject.poll_totalvotes) * 100);
      
      var pollEffect = new Fx.Tween(pollResultElement, {duration: 1000, transition: Fx.Transitions.Quad.easeOut});
      pollEffect.start('width', 3, width);
    });
  },
  
  
  pollViewMode: function(pollID)
  {
    $('poll'+pollID+'_results').style.display = "block";
    $('poll'+pollID+'_results_actions').style.display = "block";
    $('poll'+pollID+'_vote').style.display = "none";
    $('poll'+pollID+'_vote_actions').style.display = "none";
  },
  
  
  pollVoteMode: function(pollID)
  {
    $('poll'+pollID+'_results').style.display = "none";
    $('poll'+pollID+'_results_actions').style.display = "none";
    $('poll'+pollID+'_vote').style.display = "block";
    $('poll'+pollID+'_vote_actions').style.display = "block";
  },
  
  
  
  // Vote
  sendPollVote: function(pollID)
  {
    // One request at a time please
    if( this.requestsInProgress>=this.options.maxRequestsInProgress )
    {
      alert(this.Base.Language.Translate(2500115));
      return false;
    }
    
    // Get Vote Value
    var voteValue;
    
    $('sePoll'+pollID).getElements('.pollVoteOption').each(function(optionElement)
    {
      if( !optionElement.checked ) return;
      voteValue = optionElement.value;
    });
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'votepoll',
        'poll_id' : pollID,
        'vote' : voteValue
      },
      'onComplete': function(responseObject)
      {
        bind.requestsInProgress--;
        
        if( !responseObject || $type(responseObject)!="object" || responseObject.result=="failure" )
        {
          if( !responseObject.message )
            alert(bind.Base.Language.Translate(2500114));
          else
            alert(responseObject.message);
        }
        else
        {
          bind.generatePollResults(pollID, responseObject);
          bind.pollViewMode(pollID);
        }
      }
    });
    
    this.requestsInProgress++;
    request.send();
  },
  
  
  
  // Toggle Open/Closed
  togglePoll: function(pollID, isClosed)
  {
    // One request at a time please
    if( this.requestsInProgress>=this.options.maxRequestsInProgress )
    {
      alert(this.Base.Language.Translate(2500115));
      return false;
    }
    
    // Display
    var pollContainer = $('sePoll_'+pollID);
    
    if( isClosed )
    {
      pollContainer.getElement('.sePollsClose').style.display = 'none';
      pollContainer.getElement('.sePollsOpen').style.display = '';
    }
    else
    {
      pollContainer.getElement('.sePollsClose').style.display = '';
      pollContainer.getElement('.sePollsOpen').style.display = 'none';
    }
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'togglepoll',
        'poll_id' : pollID,
        'poll_closed' : (isClosed ? 1 : 0)
      },
      'onComplete': function(responseObject)
      {
        bind.requestsInProgress--;
        
        if( !responseObject || $type(responseObject)!="object" || responseObject.result=="failure" )
        {
          if( !responseObject.message )
            alert(bind.Base.Language.Translate(2500114));
          else
            alert(responseObject.message);
        }
        else
        {
          
        }
      }
    });
    
    this.requestsInProgress++;
    request.send();
  },
  
  
  
  // Delete
  deletePoll: function(pollID)
  {
    this.showPollDelete(pollID);
  },
  
  
  deletePollConfirm: function(pollID)
  {
    // One request at a time please
    if( this.requestsInProgress>=this.options.maxRequestsInProgress )
    {
      alert(this.Base.Language.Translate(2500115));
      return false;
    }
    
    // Display
    $('sePoll_'+pollID).destroy();
    
    // Ajax
    var bind = this;
    var request = new Request.JSON({
      'method' : 'post',
      'url' : this.options.ajaxURL,
      'data' : {
        'task' : 'deletepoll',
        'poll_id' : pollID
      },
      'onComplete': function(responseObject)
      {
        bind.requestsInProgress--;
        
        if( !responseObject || $type(responseObject)!="object" || responseObject.result=="failure" )
        {
          if( !responseObject.message )
            alert(bind.Base.Language.Translate(2500114));
          else
            alert(responseObject.message);
        }
      }
    });
    
    this.requestsInProgress++;
    request.send();
    
    
    
    // SHOW NEW MESSAGE
    if( $$('.sePollRow').length<1 )
      $('pollnullmessage').style.display = 'block';
  },
  
  showPollDelete: function(pollID)
  {
    var pollContainer = $('sePoll_'+pollID);
    this.currentConfirmDeleteID = pollID;
    TB_show(this.Base.Language.Translate(2500055), '#TB_inline?height=100&width=300&inlineId=confirmpolldelete', '', '../images/trans.gif');
  }

});