import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../lib/api';

function TicketDetail() {
  const { id } = useParams();
  const [ticket, setTicket] = useState(null);
  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchTicket();
    fetchComments();
  }, [id]);

  const fetchTicket = async () => {
    try {
      const response = await api.get(`/tickets/${id}`);
      setTicket(response.data);
    } catch (err) {
      setError('Failed to load ticket');
    } finally {
      setLoading(false);
    }
  };

  const fetchComments = async () => {
    try {
      const response = await api.get(`/tickets/${id}/comments`);
      setComments(response.data);
    } catch (err) {
      console.error('Failed to load comments', err);
    }
  };

  const handleAddComment = async (e) => {
    e.preventDefault();
    if (!newComment.trim()) return;

    try {
      await api.post(`/tickets/${id}/comments`, { body: newComment });
      setNewComment('');
      fetchComments();
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to add comment');
    }
  };

  const getStatusBadge = (status) => {
    const styles = {
      open: 'bg-yellow-100 text-yellow-800',
      in_progress: 'bg-blue-100 text-blue-800',
      resolved: 'bg-green-100 text-green-800',
      closed: 'bg-gray-100 text-gray-800',
    };
    return styles[status] || 'bg-gray-100 text-gray-800';
  };

  const getPriorityBadge = (priority) => {
    const styles = {
      low: 'bg-gray-100 text-gray-600',
      medium: 'bg-orange-100 text-orange-700',
      high: 'bg-red-100 text-red-700',
      urgent: 'bg-red-200 text-red-900 font-semibold',
    };
    return styles[priority] || 'bg-gray-100 text-gray-600';
  };

  if (loading) {
    return <div className="text-gray-600">Loading ticket...</div>;
  }

  if (error || !ticket) {
    return (
      <div className="bg-red-50 text-red-700 p-4 rounded">
        {error || 'Ticket not found'}
      </div>
    );
  }

  return (
    <div className="max-w-4xl">
      <div className="mb-4">
        <Link to="/tickets" className="text-blue-600 hover:underline text-sm">← Back to tickets</Link>
      </div>

      {/* Ticket Header */}
      <div className="bg-white p-6 rounded-lg shadow mb-6">
        <div className="flex justify-between items-start mb-4">
          <h1 className="text-2xl font-bold text-gray-800">{ticket.subject}</h1>
          <div className="flex gap-2">
            <span className={`px-3 py-1 text-sm rounded-full ${getStatusBadge(ticket.status)}`}>
              {ticket.status?.replace('_', ' ')}
            </span>
            <span className={`px-3 py-1 text-sm rounded-full ${getPriorityBadge(ticket.priority)}`}>
              {ticket.priority}
            </span>
          </div>
        </div>

        <p className="text-gray-700 mb-4">{ticket.description}</p>

        <div className="grid grid-cols-2 gap-4 text-sm text-gray-600 border-t pt-4">
          <div>
            <span className="font-medium">Requester:</span> {ticket.requester?.name}
          </div>
          <div>
            <span className="font-medium">Assignee:</span> {ticket.assignee?.name || 'Unassigned'}
          </div>
          <div>
            <span className="font-medium">Organization:</span> {ticket.organization?.name}
          </div>
          <div>
            <span className="font-medium">Created:</span> {new Date(ticket.created_at).toLocaleString()}
          </div>
        </div>
      </div>

      {/* Comments Section */}
      <div className="bg-white p-6 rounded-lg shadow">
        <h2 className="text-xl font-bold text-gray-800 mb-4">Comments ({comments.length})</h2>

        {comments.length === 0 ? (
          <p className="text-gray-500 text-center py-4">No comments yet.</p>
        ) : (
          <div className="space-y-4 mb-6">
            {comments.map((comment) => (
              <div key={comment.id} className={`border rounded-lg p-4 ${comment.type === 'internal' ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200'}`}>
                <div className="flex justify-between items-start mb-2">
                  <div className="flex items-center gap-2">
                    <span className="font-semibold text-sm">{comment.author?.name}</span>
                    {comment.type === 'internal' && (
                      <span className="px-2 py-0.5 text-xs bg-yellow-200 text-yellow-800 rounded">Internal</span>
                    )}
                  </div>
                  <span className="text-xs text-gray-500">{new Date(comment.created_at).toLocaleString()}</span>
                </div>
                <p className="text-gray-700 text-sm">{comment.body}</p>
              </div>
            ))}
          </div>
        )}

        {/* Add Comment Form */}
        <form onSubmit={handleAddComment} className="border-t pt-4">
          <div className="mb-3">
            <label className="block text-sm font-medium text-gray-700 mb-1">Add a comment</label>
            <textarea
              value={newComment}
              onChange={(e) => setNewComment(e.target.value)}
              rows={3}
              className="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Write your comment..."
            />
          </div>
          <button
            type="submit"
            className="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 transition"
          >
            Post Comment
          </button>
        </form>
      </div>
    </div>
  );
}

export default TicketDetail;
